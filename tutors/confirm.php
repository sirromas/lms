<?php

require_once './classes/Tutor.php';
$t = new Tutor();
$user = $_POST['user'];
$list = $t->test_page(json_decode($user));
echo $list;
