<?php
/**
 * 微服务
 * @author wsfuyibing <websearch@163.com>
 * @date   2017-12-21
 */
namespace Uniondrug\Service;

/**
 * 微服务的客户端入口
 * @method ClientResponseInterface delete(string $name, string $route, $query = null, $body = null, $extra = null)
 * @method ClientResponseInterface get(string $name, string $route, $query = null, $body = null, $extra = null)
 * @method ClientResponseInterface head(string $name, string $route, $query = null, $body = null, $extra = null)
 * @method ClientResponseInterface options(string $name, string $route, $query = null, $body = null, $extra = null)
 * @method ClientResponseInterface patch(string $name, string $route, $query = null, $body = null, $extra = null)
 * @method ClientResponseInterface post(string $name, string $route, $query = null, $body = null, $extra = null)
 * @method ClientResponseInterface put(string $name, string $route, $query = null, $body = null, $extra = null)
 * @package UniondrugServiceClient
 */
class Client
{
    /**
     * @var ClientRequest
     */
    private static $clientRequeset = null;

    /**
     * 发起Restful请求
     * @param string $name      请求方式
     * @param array  $arguments 请求参数
     * @return ClientResponseInterface
     */
    function __call($name, $arguments)
    {
        if (self::$clientRequeset === null) {
            self::$clientRequeset = new ClientRequest();
        }
        array_unshift($arguments, $name);
        return call_user_func_array([
            self::$clientRequeset,
            'run'
        ], $arguments);
    }
}
