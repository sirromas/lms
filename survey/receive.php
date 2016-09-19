<?php

require_once './classes/Survey.php';
$survey = new Survey();
$email = $_REQUEST['email'];
$result = $_REQUEST['result'];
$list = $survey->send_survey_results($email, $result);
echo $list;


