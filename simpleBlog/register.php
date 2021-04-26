<?php
include "config.php";
session_start();

if($_SESSION['is_login']){
    header('Location: index.php');
}

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
    if($result->num_rows){
        echo "user already exits;";
        header("Location: ./register.php");
        exit();
    }else{
        $sql = "insert into users(username,password) values ('$username','$password')";
        $result = $conn->query($sql);
        echo "register successfully!";
        header('Location: ./login.php');
        exit();
    }
    $conn->close();
}
?>

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <title>Register</title>  
    <link rel="stylesheet" type="text/css" href="./static/index.css"/>  
    <script src="./static/main.js"></script>
</head>  
<body>  
    <div id="login">  
        <h1>Register</h1>  
        <form method="post">  
            <input type="text" required="required" placeholder="用户名" name="username"></input>  
            <input type="password" required="required" placeholder="密码" name="password"></input>  
            <button class="but" type="submit">注册</button>  
        </form> 
        <br>
        <button class="but" onclick="window.location.href='login.php'">登录</button>
    </div>  
</body>  
</html>  


