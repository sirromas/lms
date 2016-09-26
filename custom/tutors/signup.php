<?php

require_once 'classes/Tutor.php';
$tutor = new Tutor();
$user = $_POST['user'];
$list = $tutor->tutor_signup($user);
echo $list;
