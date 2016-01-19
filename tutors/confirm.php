<?php

require_once './tutors.php';

$tutor = new Tutors();
$email = $_POST['email'];
$code = $_POST['code'];
$group = $_POST['group'];
$page=$_POST['page'];

if ($email != '' && $code != '' && $group != '' && $group != 0 && $page!='') {
    $status = $tutor->confirmTutor($email, $code, $group, $page);
} else {
    $status = 'Not all data are provided';
}
echo $status;
