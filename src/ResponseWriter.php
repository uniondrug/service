<?php
/**
 * 微服务
 *
 * @author wsfuyibing <websearch@163.com>
 * @date   2017-12-21
 */

namespace UniondrugService;

use Phalcon\Paginator\Adapter\QueryBuilder;

/**
 * 接口数据返回操作
 *
 * @package UniondrugService
 */
class ResponseWriter extends Types
{
    private $lastPaging;

    /**
     * 设置分页对象
     *
     * @param int $total    总数量
     * @param int $page     当前页
     * @param int $pageSize 每页数量
     *
     * @return $this
     */
    public function setPaging($total, $page = 1, $pageSize = 10)
    {
        $this->lastPaging = new ResponsePaging($total, $page, $pageSize);

        return $this;
    }

    /**
     * 接口返回数据错误
     *
     * @param string $error 错误原因
     * @param int    $errno 错误编号
     *
     * @return ResponseData
     * @example $this->withError('错误原因')
     * @example $this->withError('错误原因', 10001)
     */
    public function withError($error, $errno = 1)
    {
        return new ResponseData(parent::SERVICE_ERROR_TYPE, [
            "errno" => $errno ? $errno : 1,
            "error" => $error,
        ]);
    }

    /**
     * 接口返回普通数据列表
     * <code>
     * $data = [
     *     ["id" => 1],
     *     ["id" => 2]
     * ];
     * return $this->withList($data);
     * </code>
     *
     * @param array $data 二维数组
     *
     * @return ResponseData
     */
    public function withList($data)
    {
        return new ResponseData(parent::SERVICE_LIST_TYPE, $data);
    }

    /**
     * 接口返回普通对象
     * <code>
     * $data = [
     *     "id" => 1,
     *     "key" => "value"
     * ];
     * return $this->withObject($data);
     * </code>
     *
     * @param array $data 一维数组
     *
     * @return ResponseData
     */
    public function withObject($data)
    {
        return new ResponseData(parent::SERVICE_OBJECT_TYPE, $data);
    }

    /**
     * 接口返回分页数据列表
     * <code>
     * $data = [
     *     ["id" => 1],
     *     ["id" => 2]
     * ];
     * $paging = new ResponsePaging(100, 3, 15)
     * return $this->withPaging($data, $paging);
     * </code>
     *
     * @param array|QueryBuilder $data   二维数组
     * @param bool               $paging 分页结构
     *
     * @return ResponseData
     */
    public function withPaging($data, $paging = null)
    {
        if ($paging === null) {
            $paging = $this->lastPaging;
            if ($paging !== null) {
                $this->lastPaging = null;
            }
        }

        // 兼容Phalcon的Paginator结果
        if ($data instanceof QueryBuilder) {
            $res = $data->getPaginate();
            $data = $res->items->toArray();
            $paging = new ResponsePaging($res->total_items, $res->current, $res->limit);
        }

        return new ResponseData(parent::SERVICE_PAGING_LIST_TYPE, $data, $paging);
    }

    /**
     * 接口返回成功数据
     *
     * @return ResponseData
     * @example $this->withSuccess()
     */
    public function withSuccess()
    {
        return $this->withObject([]);
    }
}