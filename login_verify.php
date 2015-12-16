<?php

ini_set('error_reporting', E_ALL);
require_once 'Login.php';

$username = filter_input(INPUT_POST, "username", FILTER_VALIDATE_EMAIL);
$code = filter_input(INPUT_POST, "code", FILTER_SANITIZE_SPECIAL_CHARS);
$user_type=filter_input(INPUT_POST, "user_type", FILTER_SANITIZE_SPECIAL_CHARS);

if ($username !== false && $code !== false && $user_type!==false) {
    $login=new Login($user_type);
    $user_data = $login->verifyUser($username, $code);
    echo json_encode($user_data);
}
else {
    $user_data = array('type' => 0, 'code' => 0);
    echo json_encode($user_data);
}

