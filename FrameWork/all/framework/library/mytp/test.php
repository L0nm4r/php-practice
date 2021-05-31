<?php
namespace mytp;

use ReflectionClass;
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
        // 在此处可以添加更多别名……
    ];

    public static function get($abstract, $vars = [], $newInstance = false)
    {
        return static::getInstance()->make($abstract, $vars, $newInstance);
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    public function make($abstract, $vars = [], $newInstance = false)
    {
        if (isset($this->bind[$abstract])) {
            $abstract = $this->bind[$abstract];
        }
        // 此时$abstract的值为包含命名空间的类名
        if (isset($this->instances[$abstract]) && !$newInstance) {
            return $this->instances[$abstract];
        }
        $object = $this->invokeClass($abstract, $vars); // 该方法在下一步中实现
        if (!$newInstance) {
            $this->instances[$abstract] = $object;
        }
        return $object;
    }

    public function invokeClass($class, $vars = [])
    {
        $reflect = new ReflectionClass($class);
        // 新增代码
        if ($reflect->hasMethod('__make')) {
            $method = new ReflectionMethod($class, '__make');
            if ($method->isPublic() && $method->isStatic()) {
                $args = $this->bindParams($method, $vars);
                return $method->invokeArgs(null, $args);
            }
        }
        
        $reflect = new ReflectionClass($class);
        $constructor = $reflect->getConstructor();
        $args = $constructor ? $this->bindParams($constructor, $vars) : [];
        return $reflect->newInstanceArgs($args);
    }
    
    protected function bindParams($reflect, $vars = [])
    {
        if ($reflect->getNumberOfParameters() == 0) {
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
                $args[] = $this->make($class->getName());
            }
        }
        return $args;
    }

    public function __get($abstract)
    {
        return $this->make($abstract);
    }
    
    public function __set($abstract, $instance)
    {
        if (isset($this->bind[$abstract])) {
            $abstract = $this->bind[$abstract];
        }
        $this->instances[$abstract] = $instance;
    }
}
