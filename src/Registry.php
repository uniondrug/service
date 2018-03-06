<?php
/**
 * 微服务
 *
 * @author wsfuyibing <websearch@163.com>
 * @date   2017-12-21
 */

namespace Uniondrug\Service;

use \Phalcon\Di\Injectable;

/**
 * 服务注册
 *
 * @property \Phalcon\Config $config
 * @package UniondrugService
 */
class Registry extends Injectable
{
    private static $serverHistory = null;

    /**
     * 读取服务注册信息
     *
     * @param string $name  服务名称
     * @param string $route 路由地址
     *
     * @return string
     * @example Registry::getUrl("core", "menu/index");
     * @throws \Uniondrug\Service\Exception
     */
    public static function getUrl($name, $route)
    {
        $reg = new Registry();
        if ($reg->getDI()->has('registerClient')) {
            $node = $reg->getDI()->getShared('registerClient')->getNode($name);
        } else {
            $node = $reg->getHostByName($name);
        }
        $url = rtrim($node, '/') . '/' . ltrim($route, '/');

        return $url;
    }

    /**
     * 按服务名称获取服务所在主机
     *
     * @param string $name 微服务名称
     *
     * @return string
     * @throws Exception
     * @example $this->getHostByName('core')
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
            } catch (\Exception $e) {
                throw new Exception("can not call service configuration");
            }
        }
        $key = strtolower($name);
        if (isset(self::$serverHistory[$key])) {
            return self::$serverHistory[$key];
        }
        throw new Exception("未定义'{$name}'微服务");
    }
}