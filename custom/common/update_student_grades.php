<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_REQUEST['item'];
$list = $gr->update_student_grades(json_decode($item));
echo $list;
