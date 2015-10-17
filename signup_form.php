<?php

require ('signup_user.php');

/** 
 * @author sirromas
 * 
 */

$signup_user=new signup_user($_POST['user']) ;
$signup_form=$signup_user->getSignUpForm();
echo $signup_form;


?>