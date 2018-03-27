<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-03-27
 */
namespace Uniondrug\Service;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Phalcon\Di;
use Uniondrug\Framework\Injectable;

/**
 * @package Uniondrug\Service
 */
class ClientRequest extends Injectable
{
    const METHOD_DELETE = "DELETE";

    const METHOD_GET = "GET";

    const METHOD_HEAD = "HEAD";

    const METHOD_OPTIONS = "OPTIONS";

    const METHOD_PATCH = "PATCH";

    const METHOD_POST = "POST";

    const METHOD_PUT = "PUT";

    private static $allowMethods = [
        ClientRequest::METHOD_DELETE,
        ClientRequest::METHOD_GET,
        ClientRequest::METHOD_HEAD,
        ClientRequest::METHOD_OPTIONS,
        ClientRequest::METHOD_PATCH,
        ClientRequest::METHOD_POST,
        ClientRequest::METHOD_PUT
    ];

    private static $defaultOptions = [
        'timeout' => 30,
        'allow_redirects' => false
    ];

    /**
     * @var ClientResponse
     */
    private $lastResponse = null;

    /**
     * 执行HTTP请求
     * @param string $method
     * @param string $name
     * @param string $route
     * @param array  $query
     * @param array  $body
     * @param array  $extra
     * @return ClientResponse
     */
    public function run(string $method, string $name, string $route, $query = [], $body = [], $extra = [])
    {
        // 1. create response
        $this->lastResponse = new ClientResponse();
        // 2. request method filter
        $method = strtoupper($method);
        if (!in_array($method, self::$allowMethods)) {
            $this->lastResponse->setErrno(405);
            $this->lastResponse->setError("请求方式'{$method}'暂不支持");
            return $this->lastResponse->logger();
        }
        // 3. init http url
        $durationBegin = microtime(true);
        try {
            $url = Registry::getUrl($name, $route);
            $this->lastResponse->setUrl($method, $url);
            // 3.0 load default options
            $opts = self::$defaultOptions;
            // 3.1. Query String 的处理
            if (is_array($query) && count($query)) {
                $opts["query"] = $query;
            }
            // 3.2. Body数据请求方式: json/form/multipart
            $type = (isset($extra['type']) && !empty($extra['type'])) ? strtolower($extra['type']) : 'json';
            if (is_array($body) && count($body)) {
                if ($type == 'json') {
                    $opts['json'] = $body;
                } else if ($type == 'form') {
                    $opts['form_params'] = $body;
                } else {
                    $opts['multipart'] = $body;
                }
            }
            // 3.3. Headers 以及其他参数的附加
            if (isset($extra['headers'])) {
                $opts['headers'] = $extra['headers'];
            }
            // 3.4. timeout
            if (isset($extra['connect_timeout'])) {
                $opts['connect_timeout'] = floatval($extra['connect_timeout']);
            }
            if (isset($extra['timeout'])) {
                $opts['timeout'] = floatval($extra['timeout']);
            }
            // 3.5 send http request
            $this->callHttpClient($method, $url, $opts);
        } catch(\Exception $e) {
            $error = $e->getMessage();
            $errno = $e->getCode();
            $errno || $errno = 403;
            $this->lastResponse->setError($error, $errno);
        }
        // 4. 设置运行时长
        $this->lastResponse->setDuration(microtime(true) - $durationBegin);
        return $this->lastResponse->logger();
    }

    /**
     * 发起HTTP请求
     * @param $method
     * @param $url
     * @param $options
     * @throws Exception
     */
    private function callHttpClient($method, $url, $options)
    {
        /**
         * @var \GuzzleHttp\Client $client
         */
        $client = Di::getDefault()->getShared('httpClient');
        // 1. 请求异常处理
        try {
            /**
             * @var Response $response
             */
            $response = $client->request($method, $url, $options);
            if (!($response instanceof Response)) {
                throw new Exception("invalid response instance", 400);
            }
            /**
             * http status code error
             */
            if ($response->getStatusCode() !== 200) {
                throw new Exception("invalid response code", $response->getStatusCode());
            }
        } catch(\Exception $e) {
            throw new Exception(preg_replace("/\n.*/", '', $e->getMessage()), $e->getCode());
        }
        /**
         * 2. 无内容返回
         * @var Stream
         */
        $stream = $response->getBody();
        if (!($stream instanceof Stream)) {
            return;
        }
        /**
         * 3. 内容处理
         */
        $contents = $stream->getContents();
        $this->lastResponse->setContents($contents);
        try {
            $std = \GuzzleHttp\json_decode($contents, false);
        } catch(\Exception $e) {
            throw new Exception("invalid json response - {$e->getMessage()}", 400);
        }
        /**
         * 4. 错误类型
         */
        if (isset($std->errno) && 0 !== (int) $std->errno) {
            throw new Exception($std->error, $std->errno);
        }
        /**
         * 5. 正确返回
         */
        if (isset($std->data)) {
            $this->lastResponse->setData($std->data);
        }
    }
}
