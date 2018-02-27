<?php
/**
 * 微服务
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-12-21
 */
namespace Uniondrug\Service;

/**
 * 类型常量
 * @package UniondrugService
 */
abstract class Types
{
    /**
     * 接口返回数据错误结构
     * <code>
     * return {
     *     "errno" : "1",
     *     "error" : "错误原因"
     * }
     * </code>
     */
    const SERVICE_ERROR_TYPE = 1;
    const SERVICE_ERROR_NAME = "ERROR";
    /**
     * 接口返回普通对象结构
     * return {
     *     "errno" : "0",
     *     "error" : "",
     *     "data" : {
     *         "key" => "value"
     *     }
     * }
     */
    const SERVICE_OBJECT_TYPE = 2;
    const SERVICE_OBJECT_NAME = "OBJECT";
    /**
     * 接口返回普通数据列表
     * return {
     *     "errno" : "0",
     *     "error" : "",
     *     "data" : {
     *         [
     *             "id" => "1",
     *             "key" => "value"
     *         ],
     *         [
     *             "id" => "2",
     *             "key" => "value"
     *         ]
     *     }
     * }
     */
    const SERVICE_LIST_TYPE = 3;
    const SERVICE_LIST_NAME = "LIST";
    /**
     * 接口返回分页数据列表
     * <code>
     * return {
     *     "errno" : "0",
     *     "error" : "",
     *     "data" : {,
     *         "body" : [
     *             {
     *                 "id" : 1
     *             },
     *             { ... }
     *         ],
     *         "paging" : {
     *             "first" => 1,     // 第一页页码
     *             "prev" => 2,      // 上一页码
     *             "page" => 3,      // 当前页码
     *             "next" => 4,      // 下一页页码
     *             "last" => 5,      // 最大页码
     *             "pageSize" => 10, // 分页10条
     *             "total" => 42     // 总记录数
     *         }
     *     },
     * }
     * </code>
     */
    const SERVICE_PAGING_LIST_TYPE = 4;
    const SERVICE_PAGING_LIST_NAME = "PAGING";

    /**
     * 按类型ID获取接口返回的JSON类型名称
     *
     * @param int $typeId JSON类型ID
     *
     * @return string JSON类型名称
     * @throws Exception
     */
    public function getTypeName($typeId)
    {
        $typeName = null;
        switch ($typeId) {
            case static::SERVICE_ERROR_TYPE :
                $typeName = static::SERVICE_ERROR_NAME;
                break;
            case static::SERVICE_OBJECT_TYPE :
                $typeName = static::SERVICE_OBJECT_NAME;
                break;
            case static::SERVICE_LIST_TYPE :
                $typeName = static::SERVICE_LIST_NAME;
                break;
            case static::SERVICE_PAGING_LIST_TYPE :
                $typeName = static::SERVICE_PAGING_LIST_NAME;
                break;
        }
        if ($typeName === null) {
            throw new Exception("未知的JSON数据类型");
        }
        return $typeName;
    }

    /**
     * 是否为Error类型
     *
     * @param int $typeId JSON类型ID
     *
     * @return bool
     */
    public function isErrorType($typeId)
    {
        return (int) $typeId === static::SERVICE_ERROR_TYPE;
    }

    /**
     * 是否为Object类型
     *
     * @param int $typeId JSON类型ID
     *
     * @return bool
     */
    public function isObjectType($typeId)
    {
        return (int) $typeId === static::SERVICE_OBJECT_TYPE;
    }

    /**
     * 是否为List类型
     *
     * @param int $typeId JSON类型ID
     *
     * @return bool
     */
    public function isListType($typeId)
    {
        return (int) $typeId === static::SERVICE_LIST_TYPE;
    }

    /**
     * 是否为分页List类型
     *
     * @param int $typeId JSON类型ID
     *
     * @return bool
     */
    public function isPagingListType($typeId)
    {
        return (int) $typeId === static::SERVICE_PAGING_LIST_TYPE;
    }
}