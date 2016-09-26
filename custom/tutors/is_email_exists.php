<?php

require_once 'classes/Tutor.php';
$tutor = new Tutor();
$email = $_POST['email'];
$list = $tutor->is_email_exists($email);
echo $list;
