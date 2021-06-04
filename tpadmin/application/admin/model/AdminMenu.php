<?php
namespace app\admin\model;

use app\admin\library\Menu;
use think\Model;

class AdminMenu extends Model
{
    /**
     * 返回Menu实例
     */
    public static function tree()
    {
        $data = self::order('sort', 'asc')->select()->toArray();
        return new Menu($data);
    }
}
