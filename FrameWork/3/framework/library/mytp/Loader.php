<?php
namespace mytp;

class Loader{
    // 自动加载机制
    public static function register(){
        require __DIR__.'/../../../vendor/autoload.php';
    }
}