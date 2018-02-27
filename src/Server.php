<?php
/**
 * 微服务
 *
 * @author wsfuyibing <websearch@163.com>
 * @date   2017-12-21
 */

namespace Uniondrug\Service;

/**
 * 微服务的服务端入口
 * @method ResponseWriter setPaging(int $total, int $page = 1, int $pageSize = 10)
 * @method ResponseData withError(string $error, int $errno)
 * @method ResponseData withList(array $data)
 * @method ResponseData withObject(array $data)
 * @method ResponseData withPaging(array | \stdClass $data, ResponsePaging $paging = null)
 * @method ResponseData withSuccess()
 *
 * @package UniondrugServiceServer
 */
class Server extends \stdClass
{
    /**
     * @var ResponseWriter
     */
    private static $response;

    /**
     * Magic Dispatcher
     *
     * @param string $name      方法名称
     * @param array  $arguments 方法接受的参数
     *
     * @return ResponseData
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (self::$response === null) {
            self::$response = new ResponseWriter();
        }
        if (method_exists(self::$response, $name)) {
            return call_user_func_array([
                self::$response,
                $name,
            ], $arguments);
        }
        throw new Exception("微服务的服务端未定义'{$name}'方法");
    }
}