<?php
/**
 * 微服务
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-12-21
 */
namespace Uniondrug\Service;

use Phalcon\Http\Response;

/**
 * 接口数据返回
 */
class ResponseData extends Types
{
    private $responseData = [
        "errno" => 0,
        "error" => ""
    ];

    /**
     * 数据构造
     *
     * @param int            $typeId 数据类型
     * @param array          $data   数据体
     * @param ResponsePaging $paging 分页设置
     *
     * @throws \Uniondrug\Service\Exception
     */
    public function __construct($typeId, $data, $paging = null)
    {
        /**
         * 1. 数据格式化
         */
        is_array($data) || $data = [];
        $this->responseData['dataType'] = $this->getTypeName($typeId);
        if ($this->isErrorType($typeId)) {
            // 1.1. 错误类型
            $this->responseData['error'] = isset($data['error']) ? $data['error'] : 'unknown error';
            $this->responseData['errno'] = isset($data['errno']) ? $data['errno'] : 1;
        } else if ($this->isObjectType($typeId)) {
            // 1.2. Object类型
            $this->responseData['data'] = $data;
        } else if ($this->isListType($typeId) || $this->isPagingListType($typeId)) {
            // 1.3.1 数据列表
            $this->responseData['data'] = ["body" => $data];
            // 1.3.2 带分页的列表
            if ($this->isPagingListType($typeId) && ($paging instanceof ResponsePaging)) {
                $this->responseData['data']['paging'] = $paging->getPaging();
            }
        }
        /**
         * 2. 数据值类型转换
         */
        $this->convertValueType($this->responseData);
        /**
         * 3. JSON格式兼容
         */
        if ($this->isObjectType($typeId)) {
            // 3.1 Object模式
            $this->responseData['data'] = (object) $this->responseData['data'];
        } else if ($this->isListType($typeId) || $this->isPagingListType($typeId)) {
            // 3.2 列表模式
            $this->responseData['data']['body'] = (array) $this->responseData['data']['body'];
            if ($this->isPagingListType($typeId) && isset($this->responseData['data']['paging'])) {
                $this->responseData['data']['paging'] = (object) $this->responseData['data']['paging'];
            }
        }
    }

    /**
     * 获取返回结果
     * @return array
     */
    public function getData()
    {
        return $this->responseData;
    }

    /**
     * 获取Phalcon结果
     * @return Response
     */
    public function response()
    {
        $response = new Response();
        return $response->setJsonContent($this->responseData);
    }

    /**
     * 转换数据类型
     * 1. boolean
     * 2. null
     * 3. object
     */
    private function convertValueType(& $data)
    {
        if (is_array($data)) {
            foreach ($data as & $value) {
                // 1. 数组递归
                if (is_array($value)) {
                    $this->convertValueType($value);
                    continue;
                }
                // 2. 转字符串
                $type = strtolower(gettype($value));
                switch ($type) {
                    case "integer" :
                    case "float" :
                    case "double" :
                    case "null" :
                        $value = (string) $value;
                        break;
                    case "boolean" :
                        $value = $value ? "1" : "0";
                        break;
                }
            }
        }
    }
}