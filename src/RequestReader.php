<?php
/**
 * 微服务
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-12-21
 */
namespace UniondrugService;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * 以Restful请求微服务
 * @package UniondrugService
 */
class RequestReader extends Types
{
    private $begin = 0;
    private $finish = 0;
    private $url = '';
    private $errno = 0;
    private $error = '';
    private $contents = '';
    private $dataContents;
    private static $httpClient = null;

    /**
     * 发送HTTP请求
     *
     * @param string $method 请求方式(GET/POST等)
     * @param string $name 微服务名称
     * @param string $route 微服务路由
     * @param array  $query GET参数
     * @param array  $body POST参数
     *
     * @return RequestReader
     */
    public static function send($method, $name, $route, $query = [], $body = [])
    {
        // 1. 初始化返回结果
        $result = new RequestReader();
        $result->setBegin();
        // 2. 计算请求地址
        $url = Registry::getUrl($name, $route);
        $result->setUrl($url);
        // 3. 请求请求参数
        $options = [];
        if (is_array($query) && count($query)) {
            $options["query"] = $query;
        }
        if (is_array($body) && count($body)) {
            try {
                $options["body"] = \GuzzleHttp\json_encode($body, false);
            } catch(\Exception $e) {
            }
        }
        // 4. 发起URL请求
        $response = null;
        try {
            if (self::$httpClient === null) {
                self::$httpClient = new Client();
            }
            $response = self::$httpClient->request($method, $url, $options);
        } catch(\Exception $e) {
            $result->setError($e->getMessage(), $e->getCode());
        }
        // 5. 结果处理
        if ($response instanceof ResponseInterface) {
            $contents = $response->getBody()->getContents();
            $result->setContents($contents);
        }
        /**
         * 6. 完成并返回
         */
        $result->setFinish();
        return $result;
    }

    /**
     * @return RequestData
     */
    public function data()
    {
        return $this->dataContents;
    }

    /**
     * 读取远程返回原数据
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * 获取请求时长
     * @return float
     */
    public function getDuration()
    {
        return (float) sprintf('%.03f', $this->finish - $this->begin);
    }

    /**
     * 返回错误编号
     * @return int
     */
    public function getErrno()
    {
        return (int) $this->errno;
    }

    /**
     * 返回错误原因
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 是否有返回错误
     * @return bool
     */
    public function hasError()
    {
        return $this->getErrno() !== 0;
    }

    /**
     * 设置请求开始时间
     * @return $this
     */
    public function setBegin()
    {
        $this->begin = microtime(true);
        return $this;
    }

    /**
     * 设置请求结束时间
     * @return $this
     */
    public function setFinish()
    {
        $this->finish = microtime(true);
        return $this;
    }

    /**
     * 设置最近的错误
     *
     * @param string $error 错误原因
     * @param int    $errno 错误编号
     *
     * @return $this
     */
    public function setError($error, $errno)
    {
        $this->error = $error;
        $this->errno = (int) $errno;
        $this->errno || $this->errno = 1;
        return $this;
    }

    /**
     * 设置请求结果
     *
     * @param string $contents 远程服务器返回的内容
     *
     * @return $this
     */
    public function setContents(& $contents)
    {
        // 1. 原样返回
        $this->contents = $contents;
        // 2. JSON解析
        try {
            $arr = \GuzzleHttp\json_decode($contents, false);
            if (isset($arr->data) && ($arr->data instanceof \stdClass)) {
                $this->dataContents = new RequestData($arr->data);
            }
        } catch(\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
            return $this;
        }
        return $this;
    }

    /**
     * 设置请求URL
     *
     * @param string $url 请求URL地址
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}