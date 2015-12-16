<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/payments/Classes/PromoCode.php');

$promo=new PromoCode();
$promo->makePromoCodes();