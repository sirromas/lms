<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_REQUEST['item'];
$list = $gr->add_new_class_done(json_decode($item));
echo $list;