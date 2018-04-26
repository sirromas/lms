<?php

require_once './Grades.php';
$gr = new Grades();
$item = $_REQUEST['item'];
$list = $gr->move_students(json_decode($item));
echo $list;

