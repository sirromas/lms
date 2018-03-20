<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_REQUEST['item'];
$list = $gr->get_teacher_class_grades_table(json_decode($item));
echo $list;