<?php

require_once './Grades.php';
$gr = new Grades();
$userid = $_REQUEST['userid'];
$list = $gr->update_teachers_classes_list($userid);
echo $list;
