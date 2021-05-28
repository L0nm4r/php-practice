<?php
// 入口文件

// pathinfo模式:
$pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';


$route = [
    'student' => 'student/student/index',
    'login' => 'index/user/login'
];

$pathinfo = trim($pathinfo,'/');

if(isset($route[$pathinfo])){
    $pathinfo = $route['pathinfo'];
}

// /index.php/hello/index/1  => /hello/index/1 $_SERVER['PATH_INFO'] 依赖apache
$arr = explode('/',trim($pathinfo,'/'));
if(!isset($arr[2])){
    exit('请求信息有误!'); // arr[0] 模块 arr[1] controller arr[2] 方法
}

// 解析
list($module,$controller,$action) = $arr;

define('MODULE_PATH','../application/'.$module.'/');
$controller_name = ucwords($controller).'Controller';
$controller_path = MODULE_PATH.'Controller/'.$controller_name.'.php';

require $controller_path;
$student = new $controller_name();
$student->$action();
