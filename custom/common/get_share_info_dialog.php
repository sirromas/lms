<?php

require_once './Grades.php';
$gr = new Grades();
$item = $_REQUEST['item'];
$list = $gr->get_share_info_dialog(json_decode($item));
echo $list;