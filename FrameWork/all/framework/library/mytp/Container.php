<?php
namespace mytp;
use mytp\Request;

use ReflectionClass; // 为什么导入命名空间
use ReflectionMethod;


class Container
{
    protected static $instance;
    protected $instances = [];


    protected $bind = [
        'app' => App::class,
        'config' => Config::class,
        'request' => Request::class,
        'middleware' => Middleware::class,
        'log' => Log::class
        // others
        // 别名 => 完整命名空间
    ];


    public static function get($abstract,$vars = [], $newInstance = false)
    {
        return static::getInstance()->make($abstract,$vars,$newInstance);
    }

    // 创建自身实例 ???
    public static function getInstance()
    {
        if(is_null(static::$instance)){ // 实现单例效果,防止重复创建自身实例
            static::$instance = new static;
        }
        return static::$instance;
    }

    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

        // 生成实例并返回
    public function make($abstract,$vars = [], $newInstance = false)
    {
        if(isset($this->bind[$abstract])){
            $abstract = $this->bind[$abstract];
        }
        // 此时$abstract为包含命名空间的完整类名
        
        if(isset($this->instances[$abstract])&&!$newInstance){
            return $this->instances[$abstract];
        }

        $object = $this->invokeClass($abstract,$vars); // 实例化一个类然后返回

        if(!$newInstance){
            $this->instances[$abstract] = $object;
        } 
        return $object;
    }

    // 实例化一个类然后返回
    public function invokeClass($class, $vars = [])
    {
        $reflect = new ReflectionClass($class);
        
        /**
         * 使用类的__make方法自动化实例类
         */
        if ($reflect->hasMethod('__make')) {
            $method = new ReflectionMethod($class,'__make');
            if ($method->isPublic() && $method->isStatic()) {
                $args = $this->bindParams($method, $vars);
                return $method->invokeArgs(null,$args);
            }
        }
        
        $reflect = new ReflectionClass($class);
        $constructor = $reflect->getConstructor();

        /**
         * 参数绑定:返回完成参数绑定的关联数组
         */
        $args = $constructor ? $this->bindParams($constructor,$vars):[]; 

        return $reflect->newInstanceArgs($args);
    }

    /**
     * 构造函数参数绑定
     */
    protected function bindParams($reflect,$vars = [])
    {
        if($reflect->getNumberOfParameters() == 0) {
            return [];
        }
        
        $args = [];
        $params = $reflect->getParameters();

        foreach ($params as $param) {
            $name = $param->getName();
            $class = $param->getClass();
            if (isset($vars[$name])) {
                $args[] = $vars[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } elseif ($class) {
                /**
                 * 利用反射实现自动依赖注入, 参数? / __make
                 * 自动依赖注入的类都无参数??? 
                 * 类似App类???
                 */
                $args[] = $this->make($class->getName());
            }
        }
        return $args;
    }




    /**
     * __get/__set 通过$this->类名/别名的方式直接操作容器中的实例
     */
    public function __get($abstract)
    {
        return $this->make($abstract);
    }

    public function __set($abstract,$instance)
    {
        if (isset($this->bind[$abstract])){
            $abstract = $this->bind[$abstract];
        }

        $this->instances[$abstract] = $instance;
    }


}