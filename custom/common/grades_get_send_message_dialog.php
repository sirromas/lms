<?php

require_once './Grades.php';
$gr = new Grades();
$item = $_REQUEST['item'];
$list = $gr->grades_get_send_message_dialog(json_decode($item));
echo $list;