<?php

require_once './Classes/PlaceOrder.php';

$order=new PlaceOrder();
$email='sirromas@gmail.com';
$exp_date = time() + 31536000;
$enrol_key=$order->generateRandomString();
$status=$order->sendConfirmationEmail($email, $enrol_key, $exp_date);

if ($status) {
    echo "<p align='center'>Confirmation email was sent to $email</p>";
}
else {
    echo "<p align='center'>Email was not sent, error</p>";
}
    




