<?php

require_once './tutors.php';

$tutor = new Tutors();
$email = $_POST['email'];
$code = $_POST['code'];
$group = $_POST['group'];

if ($email != '' && $code != '' && $group != '' && $group != 0) {
    $status = $tutor->confirmTutor($email, $code, $group);
} else {
    $status = 'Not all data are provided';
}
echo $status;
