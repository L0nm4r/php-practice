<?php
namespace app\index\controller;

use mytp\facade\Config;

use mytp\Request;

class Index{
    public function index(){
        Config::set('name','xiaoming');
        return Config::get('name');
    }

    public function test(Request $req, $name = '')
    {
        var_dump($name);
        var_dump($req->get('name') === $name);
        $GLOBALS['trace'][] = '控制器Index已执行';
    }

    public function _empty($action)
    {
        return '您请求的操作' . $action . '不存在。';
    }
}
