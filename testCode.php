<?php

require_once './Login.php';
$user_type='tutor';
$login=new Login($user_type);
$email='sirromas@gmail.com';
$promo_code='td5QzhPJw1SVRBmDlx6o98n2U';
$real_code='SPhL36OeY8BXtEM7ApjoCzml1';

$promo_code_status=$login->verifyPromoCode($promo_code);
$paid_code_status=$login->verifyPaidCode($email, $real_code);

echo "Promo code status: ".$promo_code_status."<br/>";
echo "Paid code status: ".$paid_code_status."<br/>";



