<?php
/**
 * @author zhanghao <zhanghao@uniondrug.cn>
 * @date   2020-04-18
 */
namespace Uniondrug\Service;

use Uniondrug\Postman\Parsers\Annotation;


class Mock
{
    private $property = array();
    private $isArray = false;

     /**
     * 初始化mock
     * @param string $struct struct名称
     * @param string $namespace struct命名空间
     * @param string $isArray 是否为数组
     */
    public function __construct(string $struct,string $namespace='',$isArray=false)
    {
        $re = "/(\S+)\s*([\[|\]|\s]*)$/";
        preg_match($re, $struct, $m);
        $cn = trim($m[1]);
        //处理同命名空间下struct
        if ($cn[0] !== '\\') {
            $struct = '\\'.$namespace.'\\'.$cn;
        }

        //当前struct需要返回为数组
        $this->isArray = $isArray;


        //反射
        $reflect = new \ReflectionClass($struct);
        $properties = $reflect->getProperties();

        foreach($properties as $property){
            //属性mock赋值
            $mock = $this->getMockValue($property);
            if($mock['isStructType']){
                 //属性为struct 递归处理
                $propertyMock  = new Mock($mock['type'],$reflect->getNamespaceName(),$mock['isArrayType']);
                $this->property[$property->name] = $propertyMock->toArray();
            }else{
                $this->property[$property->name] = $mock['mock'];
            }
        }
    }

    

    /**
     * 返回mock数据
     * @return array
     */
    public function toArray()
    {
        if($this->isArray){
            return [$this->property];
        }else{
            return $this->property;
        }
    }

    /**
     * 获取mock数据
     */
    private function getMockValue(\ReflectionProperty $p)
    {
        $annotation = new Annotation($p);
        $annotation->type();
        $annotation->mock();
        $mock['isStructType'] = $annotation->isStructType;
        $mock['isArrayType'] = $annotation->isArrayType;
        $mock['mock'] = $annotation->mock;
        $mock['type'] = $annotation->type;

        return $mock;
    }
}
