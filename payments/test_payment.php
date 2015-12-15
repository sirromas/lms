<?php

require_once './Classes/ProcessPayment.php';

$card_no='5424000000000015';
$month_exp='06';
$year_exp='2016';

$cc=new ProcessPayment();
$cc->make_transaction($card_no, $year_exp, $month_exp);

