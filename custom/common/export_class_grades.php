<?php

require_once './Grades.php';
$gr   = new Grades();
$gid  = $_REQUEST['groupid'];
$list = $gr->export_class_grades($gid);
echo $list;

