<?php

require_once './classes/Survey.php';
$survey = new Survey();
$email = $_POST['email'];
$result = $_POST['result'];
$list = $survey->send_survey_results($email, $result);
echo $list;
