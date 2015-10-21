<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require 'CustomSignup.php';


$user=new stdClass();
$user->user_type='tutor';
$user->username='sirromas@ukr.net';
$user->email='sirromas@ukr.net';
$user->course=5;
$user->group=2;

$enroll=new CustomSignup($user) ;
$enroll->processCourseRequest();



?>