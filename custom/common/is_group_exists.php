<?php

require_once './Grades.php';
$gr    = new Grades();
$gname = $_REQUEST['gname'];
$list  = $gr->is_group_exists($gname);
echo $list;