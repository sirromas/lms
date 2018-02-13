<?php

require_once './Grades.php';
$gr     = new Grades();
$userid = $_POST['userid'];
$list   = $gr->get_grades_page( $userid );
echo $list;