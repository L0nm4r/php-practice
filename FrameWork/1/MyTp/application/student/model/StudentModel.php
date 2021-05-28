<?php
class StudentModel
{
    protected $link;
    public function __construct()
    {
        $this->link = new MySQLi('localhost', 'root', '123456', 'mytp');
        $this->link->set_charset('utf8');
    }
    public function getAll()
    {
        $sql = "SELECT * FROM `student`";
        $res = $this->link->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
