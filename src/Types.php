<?php
/**
 * 微服务类型定义
 * @link www.uniondrug.cn
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-11-06
 */

namespace UniondrugService;

/**
 * 类型常量
 * @package UniondrugService
 */
abstract class Types
{

    /**
     * 错误类型
     */
    const SERVICE_ERROR_TYPE = 1;
    /**
     * 普通对象
     */
    const SERVICE_OBJECT_TYPE = 2;
    /**
     * 普通列表
     */
    const SERVICE_LIST_TYPE = 3;
    /**
     * 分页列表
     * <code>
     * {
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
     *             "total" : "300"
     *         }
     *     },
     * }
     * </code>
     */
    const SERVICE_PAGING_LIST_TYPE = 4;

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
                $typeName = "ERROR";
                break;
            case static::SERVICE_OBJECT_TYPE :
                $typeName = "OBJECT";
                break;
            case static::SERVICE_LIST_TYPE :
                $typeName = "LIST";
                break;
            case static::SERVICE_PAGING_LIST_TYPE :
                $typeName = "PAGING";
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
     * @param int $typeId
     *
     * @return bool
     */
    public function isErrorType($typeId)
    {
        return $typeId === static::SERVICE_ERROR_TYPE;
    }

    /**
     * 是否为Object类型
     *
     * @param int $typeId
     *
     * @return bool
     */
    public function isObjectType($typeId)
    {
        return $typeId == static::SERVICE_OBJECT_TYPE;
    }

    /**
     * 是否为List类型
     *
     * @param int $typeId
     *
     * @return bool
     */
    public function isListType($typeId)
    {
        return $typeId === static::SERVICE_LIST_TYPE;
    }

    /**
     * 是否为分页List类型
     *
     * @param int $typeId
     *
     * @return bool
     */
    public function isPagingListType($typeId)
    {
        return $typeId === static::SERVICE_PAGING_LIST_TYPE;
    }
}