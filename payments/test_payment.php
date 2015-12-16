<?php

require_once './Classes/ProcessPayment.php';

/*
$card_no='4111111111111111';
$month_exp='06';
$year_exp='2016';
cds_name: name,
cds_address_1: address,
cds_city: city,
cds_state: state,
cds_zip: zip,
cds_email: email,
cds_pay_type: pay_type,
cds_cc_number: cc_number,
cds_cc_exp_month: exp_month,
ds_cc_exp_year: exp_year};
*/


$order=new stdClass();
$order->cds_name='John Doe';
$order->cds_address_1='Some address';
$order->cds_city='Come city';
$order->cds_state='AZ';
$order->cds_zip='690002';
$order->cds_email='sirromas@gmail.com';
$order->cds_pay_type='10';
$order->cds_cc_number='5424000000000015';
$order->cds_cc_exp_month='06';
$order->cds_cc_exp_year='2016';
                    
$cc=new ProcessPayment();
$cc->create_profile($order);
$cc->make_transaction($order);


