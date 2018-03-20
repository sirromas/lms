<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_REQUEST['item'];
$list = $gr->add_new_assistant(json_decode($item));
echo $list;
