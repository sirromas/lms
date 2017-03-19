<?php

require_once './classes/Survey.php';
$survey = new Survey();
$item = $_POST['item'];
$list = $survey->send_survey_email(json_decode($item));
echo $list;
