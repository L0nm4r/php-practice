<?php
namespace mytp\facade;

use mytp\Facade;

class Config extends Facade
{
    protected static function getFacadeClass()
    {
        return 'config';
    }
}