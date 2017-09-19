<?php

require_once './classes/Student.php';
$st = new Student();
$groupid = $_POST['groupid'];
$groupname = $st->get_class_data($groupid);
echo $groupname;
