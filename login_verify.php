<?php

ini_set('error_reporting', E_ALL);
require_once 'Login.php';


$username=$_POST['username'];
$user_type=$_POST['user_type'];
$code=$_POST['code'];

/*
$username='student11';
$user_type=5;
$code='862OweamvlNQBo7HjfKPhuT4z';

echo "Username: ".$username."<br/>";
echo "User type: ".$user_type."<br/>";
echo "Code: ".$code. "<br/>";
*/

if ($username !== '' && $user_type!=='') {
    $login=new Login($user_type);
    $user_data = $login->verifyUser($username, $code);    
}
else {
  $user_data = array('type' => 0, 'code' => 0);  
}
echo json_encode($user_data);
