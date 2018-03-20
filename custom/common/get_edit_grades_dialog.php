<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_REQUEST['item'];
$list = $gr->get_edit_grades_dialog(json_decode($item));
echo $list;
