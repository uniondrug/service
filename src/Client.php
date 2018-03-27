<?php
/**
 * 微服务
 *
 * @author wsfuyibing <websearch@163.com>
 * @date   2017-12-21
 */

namespace Uniondrug\Service;

/**
 * 微服务的客户端入口
 * @method RequestReader delete(string $name, string $route, array $query = [], array $body = [], array $extra = [])
 * @method RequestReader get(string $name, string $route, array $query = [], array $body = [], array $extra = [])
 * @method RequestReader head(string $name, string $route, array $query = [], array $body = [], array $extra = [])
 * @method RequestReader options(string $name, string $route, array $query = [], array $body = [], array $extra = [])
 * @method RequestReader patch(string $name, string $route, array $query = [], array $body = [], array $extra = [])
 * @method RequestReader post(string $name, string $route, array $query = [], array $body = [], array $extra = [])
 * @method RequestReader put(string $name, string $route, array $query = [], array $body = [], array $extra = [])
 *
 * @package UniondrugServiceClient
 */
class Client
{
    private static $requestMethods = [
        'DELETE',
        'HEAD',
        'GET',
        'PATCH',
        'POST',
        'PUT',
        'OPTIONS',
    ];

    /**
     * Magic Dispatcher
     *
     * @param string $name      方法名称
     * @param array  $arguments 方法接受的参数
     *
     * @return mixed
     * @throws Exception
     */
    function __call($name, $arguments)
    {
        // 1. Restful请求
        $method = strtoupper($name);
        if (in_array($method, self::$requestMethods)) {
            array_unshift($arguments, $method);

            return call_user_func_array('\Uniondrug\Service\RequestReader::send', $arguments);
        }

        // 3. 未定义
        throw new Exception("微服务的客户端未定义'{$name}'方法");
    }
}