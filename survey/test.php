<?php

require_once './classes/Survey.php';
$s=new Survey();
$item='aaa';
$s->send_survey_email($item);