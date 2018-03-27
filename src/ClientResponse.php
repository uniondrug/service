<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-03-27
 */
namespace Uniondrug\Service;

/**
 * @package Uniondrug\Service
 */
class ClientResponse implements ClientResponseInterface
{
    /**
     * @var string
     */
    private $contents = '';

    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @var string
     */
    private $method = '';

    /**
     * @var string
     */
    private $url = '';

    /**
     * 请求时长
     * @var float
     */
    private $duration = 0.0;

    /**
     * @var int
     */
    private $errno = 0;

    /**
     * @var string
     */
    private $error = "";

    /**
     * ClientResponse constructor.
     */
    public function __construct()
    {
        $this->data = new \stdClass();
    }

    /**
     * 读取请求原文
     * @return string
     */
    public function __toString()
    {
        return $this->contents;
    }

    /**
     * 设置请求原文
     * @param string $contents
     * @return $this
     */
    public function setContents(string $contents)
    {
        $this->contents = $contents;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 设置数据结果集
     * @param \stdClass $data
     * @return $this
     */
    public function setData(\stdClass $data = null)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * 设置运行时间
     * @param float $duration
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getErrno()
    {
        return $this->errno;
    }

    /**
     * 设置错误编号
     * @param int $errno
     * @return $this
     */
    public function setErrno(int $errno)
    {
        $this->errno = (int) $errno;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误原因
     * @param string $error
     * @return $this
     */
    public function setError(string $error, int $errno = null)
    {
        $this->error = (string) $error;
        if ($errno !== null) {
            $this->setErrno($errno);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasError()
    {
        return $this->errno !== 0;
    }

    /**
     * 写入日志
     * @return $this
     */
    public function logger()
    {
        return $this;
    }

    /**
     * 设置请求记录
     * @param string $method
     * @param string $url
     * @return $this
     */
    public function setUrl(string $method, string $url)
    {
        $this->method = $method;
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        try {
            return \GuzzleHttp\json_decode(\GuzzleHttp\json_encode($this->data), true);
        } catch(\Exception $e) {
            return [];
        }
    }
}
