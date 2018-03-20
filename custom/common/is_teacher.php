<?php

require_once './Grades.php';
$gr     = new Grades();
$userid = $_REQUEST['userid'];
$list   = $gr->is_teacher_level($userid);
echo $list;