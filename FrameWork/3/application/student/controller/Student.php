<?php
namespace app\student\controller;

use app\student\model\Student as StudentModel;
use think\helper\Time;

class Student{
    public function index(){
        // require MODULE_PATH.'model/Student.php';
        $model = new StudentModel();
        $data = $model->getAll();
        require MODULE_PATH.'view/student.html';
    }
    //think-helper 包测试
    public function test(){
        var_dump(Time::today());
    }
}