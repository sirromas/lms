<?php

require_once './classes/Tutor.php';
$t = new Tutor();
$email = $_POST['email'];
$url = $_POST['url'];
$list = $t->test_page($email, $url);
echo $list;
