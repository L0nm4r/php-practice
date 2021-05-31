<?php
namespace mytp;
use ReflectionMethod;
use Exception;

class App extends Container
{
    protected $initialized = false;
    protected $rootPath;
    protected $appPath;
    protected $configPath;

    public function __construct()
    {
        $scriptName = $_SERVER['SCRIPT_FILENAME'];
        $this->rootPath = dirname(realpath(dirname($scriptName))) . '/';
        $this->appPath = $this->rootPath . 'application/';
        $this->configPath = $this->rootPath . 'config/';
    }


    public function run()
    {
        try{
            $this->initialize(); // 初始化 加载实例,配置文件

            // $this->init($this->config->get('app.default_module'));
            // var_dump($this->config->get('app.app_debug'));      // bool(true)
            // var_dump($this->config->get('app.default_action')); // string(4) "test"
            // exit;
    
            // $module = $this->config->get('app.default_module');
            // $this->init($module);
            // $controller = $this->config->get('app.default_controller');
            // $action = $this->config->get('app.default_action');
            // $instance = $this->make('\\app\\' . $module . '\\controller\\' . $controller);
            // return Response::create($instance->$action());    
            // $dispatch = $this->routeCheck();
            // return Response::create(implode(',', $dispatch));
        
            //return $this->dispatch($this->routeCheck()); // 路由分发 
            
            $dispatch = $this->routeCheck();
            $this->middleware->add(function (Request $request, $next) use ($dispatch) {
                return $this->dispatch($dispatch);
            });
            return $this->middleware->dispatch($this->request);
        }catch(Exception $e){
            exit('系统发生错误 '.($this->config->get('app.app_debug')?$e->getMessage():''));
        }
    }

    public function dispatch(array $dispatch)
    {
        list($module, $controller, $action) = $dispatch;
        $this->request->setModule($module);
        $this->request->setController($controller);
        $this->request->setAction($action);
        $instance = $this->controller($controller); //获取控制器实例(控制器->class)
        // return Response::create($instance->$action());

        /**
         * 在下面调用controller中的方法
         */
        if (is_callable([$instance, $action])) {
            $reflect = new ReflectionMethod($instance, $action);
            $this->request->setAction($action);
            $vars = $this->request->get();
        } elseif (is_callable([$instance, '_empty'])) {
            $call = [$instance, '_empty'];
            $vars = ['action' => $action];
            $reflect = new ReflectionMethod($instance, '_empty');
        } else {
            // exit('操作不存在：' . get_class($instance) . '/' . $action . '()');
            throw new Exception('操作不存在：' . get_class($instance) . '/' . $action . '()');
        }

        $args = $this->bindParams($reflect, $vars);
        $data = $reflect->invokeArgs($instance, $args); // 带参数执行
        return Response::create($data);
    }

    public function controller($name)
    {
        if (strpos($name, '/')) { // ?? 没看明白
            list($module, $name) = explode('/', $name);
        } else {
            $module = $this->request->module();
        }
        $class = '\\app\\' . $module . '\\controller\\' . $name;
        if (!class_exists($class)) {
            // exit('请求的控制器' . $class . '不存在！');
            throw new Exception('请求的控制器' . $class . '不存在！');
        }
        return $this->make($class);
    }

    /**
     * 路由检测
     */    
    public function routeCheck()
    {
        $path = $this->request->path(); // 获取请求path
        /**
         * 路由匹配
         */
        $routeFile = $this->rootPath . 'route/route.php';
        $rule = is_file($routeFile) ? include $routeFile : [];
        foreach ($rule as $k => $v) {
            $k = str_replace('/', '\/', $k);
            if (preg_match('/^' . $k . '(\/.*)*$/', $path)) {
                $path = $v;
                break;
            }
        }
        /**
         * 模块 控制器 方法 检测
         */
        $arr = $path === '' ? [] : explode('/', trim($path, '/'));
        $module = isset($arr[0]) ? $arr[0] : $this->config->get('app.default_module');
        $this->init($module); // 初始化模块
        $controller = isset($arr[1]) ? $arr[1] : $this->config->get('app.default_controller');
        $action = isset($arr[2]) ? $arr[2] : $this->config->get('app.default_action');
        $controller = ucfirst($controller);
        foreach ([$module, $controller, $action] as $v) {
            if (!preg_match('/^[A-Za-z]\w{0,20}$/', $v)) {
                // exit('请求参数包含特殊字符！');
                throw new Exception('请求参数包含特殊字符！');
            }
        }

        return [$module, $controller, $action];
    }

    public function initialize()
    {
        if($this->initialized){
            return;
        }
        $this->initialized = true;

        static::setInstance($this);
        $this->app = $this;
        // 加载配置文件
        $this->config->set(include $this->rootPath . 'framework/convention.php'); // 惯例配置
        $this->init(); // 初始化应用/模块
        ini_set('display_errors', $this->config->get('app.app_debug'));
        date_default_timezone_set($this->config->get('app.default_timezone'));
    }

    public function init($module = '')
    {
        $module = $module ? $module . '/' : '';
        $path  = $this->appPath . $module;
        if (is_dir($path . 'config')) {
            $dir = $path . 'config/';
        } elseif (is_dir($this->configPath . $module)) {
            $dir = $this->configPath . $module;
        }
        $files = (isset($dir) && is_dir($dir)) ? scandir($dir) : [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $this->config->set(pathinfo($file, PATHINFO_FILENAME), include $dir . $file);
            }
        }

        /**
         * 加载中间件文件
         */
        if (is_file($path . 'middleware.php')) {
            $this->middleware->import(include $path . 'middleware.php');
        }
    }

    
    public function getRootPath()
    {
        return $this->rootPath;
    }

    public function getAppPath()
    {
        return $this->appPath;
    }
}