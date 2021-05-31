<?php
namespace mytp;

class Facade
{
    // 在调用的静态方法不存在时自动调用.
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::createFacade(),$method],$params);
    }

    protected static function createFacade($class = '', $args = [])
    {
        $class = $class ? : static::class;
        $facadeClass = static::getFacadeClass();
        if($facadeClass){
            $class = $facadeClass;
        }
        return Container::getInstance()->make($class,$args);
    }

    protected static function getFacadeClass()
    {

    }
}