<?php
/**
 * 微服务
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-12-21
 */
namespace Uniondrug\Service;

/**
 * 微服务请求结果的数据结构
 */
class RequestData extends Types
{
    private $_ = [];
    private $__ = null;

    /**
     * 构造数据结构对象
     *
     * @param \stdClass|null $stdClass
     */
    function __construct(\stdClass & $stdClass = null)
    {
        // 1. not standard class
        if (!($stdClass instanceof \stdClass)) {
            return;
        }
        // 2. detect
        foreach ((array) $stdClass as $key => & $value) {
            // 2.1 inner standard class
            if ($value instanceof \stdClass) {
                $this->_[$key] = new RequestData($value);
                continue;
            }
            // 2.2 is array
            if (is_array($value)) {
                $this->_[$key] = [];
                foreach ($value as $subValue) {
                    $this->_[$key][] = new RequestData($subValue);
                }
                continue;
            }
            // 2.3 property
            $this->_[$key] = $value;
        }
    }

    /**
     * 读取指定属性值
     *
     * @param string $name 属性名称
     *
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (isset($this->_[$name])) {
            return $this->_[$name];
        }
        throw new Exception("call undefined '{$name}' property");
    }

    /**
     * 只读属性禁止修改
     *
     * @param string $name 属性名称
     * @param mixed  $value 新值
     *
     * @throws Exception
     */
    function __set($name, $value)
    {
        throw new Exception("can not change '{$name}' value on readonly property");
    }

    /**
     * 对象转数组
     * @return array
     */
    public function toArray()
    {
        if ($this->__ === null) {
            $tmp = $this->_;
            foreach ($tmp as & $value) {
                if ($value instanceof RequestData) {
                    $value = $value->toArray();
                } else if (is_array($value)) {
                    foreach ($value as & $subValue) {
                        if ($subValue instanceof RequestData) {
                            $subValue = $subValue->toArray();
                        }
                    }
                }
            }
            $this->__ = $tmp;
        }
        return $this->__;
    }
}