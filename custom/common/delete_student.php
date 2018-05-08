<?php

require_once './Grades.php';
$gr = new Grades();
$item = $_REQUEST['item'];
$gr->delete_student(json_decode($item));
