<?php
namespace mytp;

class Middleware
{
    protected $queue = []; // 中间件队列
    protected $app; // App实例

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 导入一个中间件
     */
    public function import(array $middlewares = [])
    {
        foreach ($middlewares as $middleware) {
            $this->add($middleware);
        }
    }

    public function add($middleware)
    {
        if ($middleware instanceof \Closure) { // 闭包函数
            $this->queue[] = $middleware;
        } else {
            $this->queue[] = [$this->app->make($middleware), 'handle']; // 交给call_user_func_array调用,前面是class后面是action
        }
    }

    public function dispatch(Request $request)
    {
        return call_user_func($this->resolve(), $request);
    }

    protected function resolve()
    {
        return function (Request $request) {
            $middleware = array_shift($this->queue); // 出栈
            return call_user_func_array($middleware, [$request, $this->resolve()]); // 递归执行
        };
    }
}
