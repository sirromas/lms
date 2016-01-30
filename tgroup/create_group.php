<?php

require_once './Course.php';
$cs=new Course();
$email=$_POST['email'];
$code=$_POST['code'];
$page=$_POST['page'];
$group1=$_POST['group1'];
$group2=$_POST['group2'];
$group3=$_POST['group3'];
$group4=$_POST['group4'];
$groups=array($group1,$group2,$group3,$group4);
$response=$cs->create_new_groups($email, $code, $page, $groups);
echo $response;

