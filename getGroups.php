<?php
require_once 'signup_user.php';
$groups = new signup_user('student');

if ($_POST['course'] != '') {
    $list = $groups->getGroupsList('student', $_POST['course']);
} elseif ($_POST['email'] != '') {
    $list = $groups->isEmailUsed($_POST['email']);
} elseif ($_POST['username'] != '') {
    $list = $groups->isUserUsed($_POST['username']);
}
echo $list;

?>