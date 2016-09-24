<?php

require_once './classes/Survey.php';
$survey = new Survey();
$email = $_POST['email'];
$list = $survey->send_survey_email($email);
echo $list;
