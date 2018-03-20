<?php

require_once './Grades.php';
$gr     = new Grades();
$userid = $_REQUEST['userid'];
$list   = $gr->is_student_has_ppicture($userid);
echo $list;