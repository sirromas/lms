<?php

require_once 'classes/Student.php';
$st = new Student();
$list = $st->get_groups_list();
echo $list;
