<?php

require_once './Grades.php';
$gr = new Grades();
$item = $_REQUEST['item'];
$list = $gr->send_grades_feedback(json_decode($item));
echo $list;
