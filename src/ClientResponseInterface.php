<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-03-27
 */
namespace Uniondrug\Service;

/**
 * @package Uniondrug\Service
 */
interface ClientResponseInterface
{
    /**
     * 转为字符串输出
     * @return string
     */
    public function __toString();

    /**
     * 读取结果的STD对象
     * @return \stdClass
     */
    public function getData();

    /**
     * 读取运行编号
     * @return double
     */
    public function getDuration();

    /**
     * 读取错误编号
     * @return int
     */
    public function getErrno();

    /**
     * 读取错误原因
     * @return string
     */
    public function getError();

    /**
     * 请求结果是否有错误
     * @return boolean
     */
    public function hasError();

    /**
     * 转为数组
     * @return array
     */
    public function toArray();
}
