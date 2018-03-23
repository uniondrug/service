<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-03-23
 */

namespace Uniondrug\Service;

use Phalcon\Http\Response;
use Uniondrug\Structs\ListStruct;
use Uniondrug\Structs\PaginatorStruct;
use Uniondrug\Structs\StructInterface;

/**
 * 服务端返回
 *
 * @package Uniondrug\Service
 */
class Server
{
    const DATA_TYPE_ERROR = 'ERROR';
    const DATA_TYPE_LIST = 'LIST';
    const DATA_TYPE_OBJECT = 'OBJECT';
    const DATA_TYPE_PAGING = 'PAGING';

    /**
     * @param string          $name
     * @param StructInterface $arguments
     *
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        throw new \Exception("method '{$name}()' is deprecated, please use 'withStruct()' instead.");
    }

    /**
     * @param string $error
     * @param int    $errno
     *
     * @return Response
     */
    public function withError(string $error, $errno = 1)
    {
        if ((int) $errno === 0) {
            $errno = 1;
        }

        return $this->response([], static::DATA_TYPE_ERROR, $error, $errno);
    }

    /**
     * 返回Struct结果
     *
     * @param StructInterface $struct
     *
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
     * 格式化输出结构
     *
     * @param array  $data
     * @param string $dataType
     * @param string $error
     * @param int    $errno
     *
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

        // 4. 返回结果
        return (new Response())->setJsonContent([
            'errno'    => (string) $errno,
            'error'    => (string) $error,
            'dataType' => $dataType,
            'data'     => (object) $data,
        ]);
    }

    /**
     * 类型模糊化
     *
     * @param array $data
     *
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
