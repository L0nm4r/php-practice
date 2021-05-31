<?php
namespace app\http\middleware;

class Before
{
    public function handle($request, \Closure $next)
    {
        $GLOBALS['trace'][] = ('中间件Before已执行');
        return $next($request);
    }
}
