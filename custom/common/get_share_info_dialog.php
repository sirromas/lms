<?php

require_once './Grades.php';
$gr     = new Grades();
$userid = $_REQUEST['userid'];
$list   = $gr->get_share_info_dialog($userid);
echo $list;