<?php
/**
 * 微服务
 * @author wsfuyibing <websearch@163.com>
 * @date   2017-12-21
 */
namespace Uniondrug\Service;

use Phalcon\Di;
use Uniondrug\Framework\Injectable;

/**
 * 服务注册
 * @property \Phalcon\Config $config
 * @package UniondrugService
 */
class Registry extends Injectable
{
    private static $serverHistory = null;
    private static $serviceDefinitions = [
        'wxapi' => 'wxapi',
        'wss' => 'wss.backend',
        'module.token' => 'token.module',
        'product.module' => 'product.module',
        'payments' => 'payment.module',
        'promotionUser' => 'promotion.user.module',
        'promotionBidding' => 'promotion.bidding.module',
        'promotionFinance' => 'promotion.finance.module',
        'finance' => 'finance.union'
    ];
    /**
     * 环境域名
     * 应用于NS向Consul迁移的过渡期内(SDK < 2.0), 使用如下规则
     * 匹配
     * @var array
     * @date 2018-11-12
     */
    private static $serviceDomains = [
        'production' => 'uniondrug.cn',
        'release' => 'turboradio.cn',
        'testing' => 'test.dovecot.cn',
        'development' => 'dev.dovecot.cn',
    ];
    private static $serviceSuffix = null;
    private static $serviceEmptySuffix = "localhost";

    /**
     * 读取服务注册信息
     * @param string $name  服务名称
     * @param string $route 路由地址
     * @return string
     * @example Registry::getUrl("core", "menu/index");
     * @throws \Uniondrug\Service\Exception
     */
    public static function getUrl($name, $route)
    {
        // 1. ws|wss|http/https类完整地址
        //    日期: 2018-11-12
        if (preg_match("/^(ws|wss|http|https):\/+/", $name) > 0) {
            $url = rtrim($name, "/");
            if ($route !== '') {
                $route = ltrim($route, "/");
                $url .= "/{$route}";
            }
            return $url;
        }
        // 2. 兼容历史
        //    在NS中注释的映射关系迁移至Consul
        //    在过渡期内, 使用已绑定的历史记录
        //    SDK升级后, 自动切换至2.x版本
        //    日期: 2018-11-12
        if (preg_match("/^[_a-zA-Z0-9\-\.]+$/", $name) > 0) {
            // 3. 域名后缀
            if (self::$serviceSuffix === null) {
                $di = Di::getDefault();
                $env = $di->environment();
                self::$serviceSuffix = isset(self::$serviceDefinitions[$env]) ? self::$serviceDefinitions[$env] : self::$serviceEmptySuffix;
            }
            // 4. 域名前缀
            $prefix = isset(self::$serviceDefinitions[$name]) ? self::$serviceDefinitions[$name] : "{$name}.module";
            $url = "http://{$prefix}.".self::$serviceSuffix;
            if ($route !== '') {
                $route = ltrim($route, "/");
                $url .= "/{$route}";
            }
            // 5. 返回地址
            return $url;
        }
        // 6. 废弃方式
        throw new \RuntimeException("Registry - cannot get an upstream for service '$name'");
    }

    /**
     * 按服务名称获取服务所在主机
     * @param string $name 微服务名称
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
            } catch(\Exception $e) {
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
