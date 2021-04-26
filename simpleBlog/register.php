<?php
include "config.php";
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

if($username && $password){
    // 数据库连接
    $conn = new mysqli($host,$dbuser,$dbpasswd,$dbname,$dbport);
    if($conn->connect_error){
        die("数据库连接失败!".$conn->connect_error);
    }

    // 处理数据
    $username = addslashes($username);
    $password = md5($password);
    $sql = "select * from users where username = '$username'";

    $result = $conn->query($sql);
    var_dump($result);
    if($result->num_rows){
        echo "user already exits;";
        header("Location: ./register.html");
        exit();
    }else{
        $sql = "insert into users(username,password) values ('$username','$password')";
        $result = $conn->query($sql);
        echo "register successfully!";
        header('Location: ./login.html');
        exit();
    }
    $conn->close();
}
header("Location: ./register.html");
exit();


