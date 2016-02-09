<?php

require_once './Login.php';
$userid=$_POST['userid'];
$code=$_POST['code'];
$login=new Login();
$list=$login->updateStudentEnrollKey($userid, $code);
echo $list;


