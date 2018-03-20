<?php

require_once './Grades.php';
$gr   = new Grades();
$list = $gr->get_add_new_class_dialog();
echo $list;