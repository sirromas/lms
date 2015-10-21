<?php

ini_set('error_reporting', E_ALL);
require_once 'Login.php';
$login=new Login($_POST['user_type']);

$emailStatus=$login->verifyEmail($_POST['username']);
$passwordStatus=$login->verifyPassword($_POST['password']);
$codeStatus=$login->verifyCode($_POST['code']);

$user_data=array('username'=>$emailStatus,'password'=>$passwordStatus,'code'=>$codeStatus);
echo json_encode($user_data);





?>