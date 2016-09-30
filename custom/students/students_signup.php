<?php

require_once 'classes/Student.php';
$st = new Student();
$user = $_POST['user'];
$list = $st->student_signup($user);
echo $list;
