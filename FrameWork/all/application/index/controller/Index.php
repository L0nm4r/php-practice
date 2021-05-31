<?php
namespace app\index\controller;

use mytp\Controller;
use mytp\facade\Config;

use mytp\Request;

class Index extends Controller{
    public function index(){
        Config::set('name','xiaoming');
        return Config::get('name');
    }

    public function test()
    {
        // 调用assign()方法为模板中的变量赋值，格式为assign(变量名, 值)
        $this->Smarty->assign('title', 'Smarty');
        $this->Smarty->assign('desc', 'Smarty是一个PHP的模板引擎');
        // 调用fetch()方法渲染模板文件，返回渲染的HTML结果字符串
        return $this->Smarty->fetch('test.html');
    }

    public function _empty($action)
    {
        return '您请求的操作' . $action . '不存在。';
    }
}
