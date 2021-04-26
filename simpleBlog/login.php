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

?>
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <title>Login</title>  
    <link rel="stylesheet" type="text/css" href="./static/index.css"/>  
    <script src="./static/main.js"></script>
</head>  
<body>  
    <div id="login">  
        <h1>Login</h1>  
        <form method="POST">  
            <input type="text" required="required" placeholder="用户名" name="username"></input>  
            <input type="password" required="required" placeholder="密码" name="password"></input>  
            <button class="but" value="submit">登录</button>  
        </form> 
        <br>
        <button class="but" onclick="window.location.href='register.php'">注册</button> 
    </div>  
</body>  
</html>