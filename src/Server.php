<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-03-23
 */
namespace Uniondrug\Service;

use Phalcon\Di;
use Phalcon\Http\Response;
use Uniondrug\Framework\Container;
use Uniondrug\Structs\ListStruct;
use Uniondrug\Structs\PaginatorStruct;
use Uniondrug\Structs\StructInterface;

/**
 * 服务端返回
 * @package Uniondrug\Service
 */
class Server
{
    const DATA_TYPE_ERROR = 'ERROR';
    const DATA_TYPE_LIST = 'LIST';
    const DATA_TYPE_OBJECT = 'OBJECT';
    const DATA_TYPE_PAGING = 'PAGING';

    /**
     * 执行未定义方法时触发
     * @param string $name      方法名
     * @param array  $arguments 参数数组
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        throw new \Exception("method '{$name}()' is deprecated, please use 'withStruct()' instead.");
    }

    /**
     * 返回错误Response
     * @param string $error 错误原因
     * @param int    $errno 错误编号
     * @return Response
     */
    public function withError(string $error, $errno = 1)
    {
        if ((int) $errno === 0) {
            $errno = 1;
        }
        /**
         * 非production环境下, 错误消费中追加应用名称
         * @var Container $di
         */
        $di = Di::getDefault();
        if ('production' !== $di->environment()) {
            $error = '['.$di->getConfig()->path('app.appName').'] - '.$error;
        }
        // 返回Response
        return $this->response([], static::DATA_TYPE_ERROR, $error, $errno);
    }

    /**
     * 返回成功Response
     * @param array|null $data 数据格式可选
     * @return Response
     */
    public function withSuccess(array $data = null)
    {
        return $this->response(is_array($data) ? $data : [], static::DATA_TYPE_OBJECT, "", 0);
    }

    /**
     * 以StructInterface返回Response
     * @param StructInterface $struct
     * @return Response
     */
    public function withStruct(StructInterface $struct)
    {
        $dataType = static::DATA_TYPE_OBJECT;
        if (is_subclass_of($struct, ListStruct::class, true)) {
            $dataType = static::DATA_TYPE_LIST;
        } else if (is_subclass_of($struct, PaginatorStruct::class, true)) {
            $dataType = static::DATA_TYPE_PAGING;
        }
        return $this->response($struct->toArray(), $dataType, '', 0);
    }

    /**
     * 原样Object返回
     * @param array $data
     * @return Response
     * @example $obj->serviceServer->withData([
     *     'key' => 'value'
     *     ]);
     */
    public function withData(array $data)
    {
        return $this->response($data, self::DATA_TYPE_OBJECT, '', 0);
    }

    /**
     * 原样List返回
     * @param array $data
     * @return Response
     * @example $obj->serviceServer->withListData([
     *     ['key' => 'value'],
     *     ['key' => 'value-2']
     *     ]);
     */
    public function withListData(array $data)
    {
        return $this->response(['body' => $data], self::DATA_TYPE_LIST, '', 0);
    }

    /**
     * 格式化输出结构
     * @param array  $data
     * @param string $dataType
     * @param string $error
     * @param int    $errno
     * @return Response
     */
    private function response(array $data, string $dataType, $error, $errno)
    {
        // 1. 类型转换
        if (count($data) > 0) {
            $data = $this->parseData($data);
            // 2. List类型
            if ($dataType === static::DATA_TYPE_LIST || $dataType === static::DATA_TYPE_PAGING) {
                $data['body'] = isset($data['body']) && is_array($data['body']) ? $data['body'] : [];
            }
            // 3. Paging类型
            if ($dataType === static::DATA_TYPE_PAGING) {
                $data['paging'] = (object) (isset($data['paging']) && is_array($data['paging']) ? $data['paging'] : []);
            }
        }
        /**
         * 2. Response
         * @var Response $response
         */
        return (new Response())->setJsonContent([
            'errno' => (string) $errno,
            'error' => (string) $error,
            'dataApp' => 'application',
            'dataType' => $dataType,
            'data' => (object) $data,
        ]);
    }

    /**
     * 类型模糊化
     * @param array $data
     * @return array
     */
    private function parseData(array $data)
    {
        foreach ($data as & $value) {
            $type = strtolower(gettype($value));
            switch ($type) {
                case 'array' :
                    $value = $this->parseData($value);
                    break;
                case 'boolean' :
                    $value = $value ? '1' : '0';
                    break;
                case 'integer' :
                case 'float' :
                case 'double' :
                    $value = (string) $value;
                    break;
                case
                'null' :
                    $value = '';
                    break;
            }
        }
        return $data;
    }
}
