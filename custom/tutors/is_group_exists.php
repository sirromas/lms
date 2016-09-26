<?php

require_once 'classes/Tutor.php';
$tutor = new Tutor();
$groupname = $_POST['groupname'];
$list = $tutor->is_group_exists($groupname);
echo $list;
