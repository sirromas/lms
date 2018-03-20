<?php

require_once './Grades.php';
$gr     = new Grades();
$userid = $_REQUEST['userid'];
$list   = $gr->get_add_assistance_dialog($userid);
echo $list;