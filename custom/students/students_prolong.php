<?php

require_once 'classes/Student.php';
$st = new Student();
$user = $_POST['user'];
$list = $st->prolong_subscription($user);
echo $list;
