<?php

require_once './signup_user.php';
$user_type='tutor';
$name=$_POST['new_group_name'];
$user=new signup_user($user_type);
$data=$user->isGroupExists($name);
echo $data;