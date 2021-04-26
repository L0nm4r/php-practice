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

    $username = addslashes($username);
    $password = md5($password);
    $sql = "select * from users where username = '$username' and password='$password'";

    $result = $conn->query($sql);
    var_dump($result);
    if($result->num_rows){
        $_SESSION['is_login'] = true;
        header('Location: ./index.php');
        exit();
    }else{
        echo "username/password wrong!";
    }
    $conn->close();
}
header("Location: ./login.html");
exit();
