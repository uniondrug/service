<?php
/**
 * 微服务注册
 * @link www.uniondrug.cn
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-11-06
 */

namespace UniondrugService;

use \Phalcon\Di\Injectable;

/**
 * 服务注册中心
 * @property \Phalcon\Config $config
 * @package UniondrugService
 */
class Registry extends Injectable
{

    private static $serverHistory = null;

    /**
     * 读取服务注册信息
     * <code>
     * $url1 = Registry::getUrl("core", "menu/index");
     * $url2 = Registry::getUrl("core", "menu/index", ["key" => "value"]);
     * </code>
     *
     * @param string $name 服务名称
     * @param string $route 路由地址
     *
     * @return string
     */
    public static function getUrl($name, $route)
    {
        $reg = new Registry();
        $url = $reg->getHostByName($name).$route;
        return $url;
    }

    /**
     * 按服务名称获取服务所在主机
     * <code>
     * $serviceName = 'order';
     * $serviceUrl = $this->getHostByName($serviceName);
     * // return 'http://order.service.uniondrug.cn/'
     * </code>
     *
     * @param string $name
     *
     * @return string
     * @throws Exception
     */
    public function getHostByName($name)
    {
        if (self::$serverHistory === null) {
            try {
                $history = (array) $this->config->get("services");
                if (is_array($history)) {
                    foreach ($history as $key => $value) {
                        $value = preg_replace("/\/+$/", "", trim($value));
                        if ($value !== "") {
                            $key = strtolower($key);
                            self::$serverHistory[$key] = $value;
                        }
                    }
                }
            } catch(\Exception $e) {
                throw new Exception("can not call service configuration");
            }
        }

        $key = strtolower($name);
        if (isset(self::$serverHistory[$key])) {
            return self::$serverHistory[$key];
        }

        throw new Exception("call '{$name}' undefined service");
    }
}