<?php
namespace app\http\middleware;

class After
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $GLOBALS['trace'][] = ('中间件After已执行');
        $response->data(var_export($GLOBALS['trace'], true));
        return $response;
    }
}
