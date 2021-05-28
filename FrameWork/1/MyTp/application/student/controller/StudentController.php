<?php
class StudentController{
    public function index(){
        require MODULE_PATH.'model/StudentModel.php';
        $model = new StudentModel();
        $data = $model->getAll();
        require MODULE_PATH.'view/student.html';
    }
}