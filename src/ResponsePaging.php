<?php
/**
 * 微服务
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-12-21
 */
namespace Uniondrug\Service;

/**
 * 接口数据返回分页
 * @package UniondrugService
 */
class ResponsePaging extends \stdClass
{
    private $pagingData = [
        "first" => 0,
        "last" => 0,
        "next" => 0,
        "page" => 0,
        "pageSize" => 10,
        "prev" => 0,
        "total" => 0
    ];

    /**
     * 分页构造
     *
     * @param int $total 总数
     * @param int $page 当前页码
     * @param int $pageSize 每页数量
     *
     * @throws Exception
     */
    public function __construct($total, $page = 1, $pageSize = 10)
    {
        /**
         * 每页
         */
        $pageSize = is_numeric($pageSize) && $pageSize > 0 ? (int) $pageSize : 0;
        if ($pageSize <= 0) {
            throw new Exception("每页数量不能小于'0'整数");
        }
        /**
         * 总数
         */
        $total = is_numeric($total) && $total > 0 ? (int) $total : 0;
        if ($total <= 0) {
            return;
        }
        /**
         * 基础值
         */
        $page = is_numeric($page) && $page > 1 ? (int) $page : 1;
        $this->pagingData["total"] = $total;
        $this->pagingData["page"] = $page;
        $this->pagingData["pageSize"] = $pageSize;
        $this->pagingData["first"] = 1;
        $this->pagingData["last"] = ceil($total / $pageSize);
        if ($this->pagingData["page"] > 1) {
            $this->pagingData["prev"] = $this->pagingData["page"] - 1;
        }
        if ($this->pagingData["page"] < $this->pagingData["last"]) {
            $this->pagingData["next"] = $this->pagingData["page"] + 1;
        }
    }

    /**
     * 读取分页结果
     * @return array
     */
    public function getPaging()
    {
        return $this->pagingData;
    }
}