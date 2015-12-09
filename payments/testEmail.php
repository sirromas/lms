<?php

require_once './Classes/PlaceOrder.php';

$order=new PlaceOrder();
$email='sirromas@gmail.com';
$exp_date = time() + 31536000;
$enrol_key=$order->generateRandomString();
$order->sendConfirmationEmail($email, $enrol_key, $exp_date);



