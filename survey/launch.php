<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './classes/Survey.php';
$survey = new Survey();
//$email = 'sirromas@gmail.com';
//$email = 'sirromas@ukr.net';
$email = 'steve@posnermail.com';
$list = $survey->send_survey_email($email);
echo $list;
