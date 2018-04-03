<?php

require_once './Grades.php';
$gr = new Grades();
$item = $_REQUEST['item'];
$list = $gr->get_add_assistance_dialog(json_decode($item));
echo $list;