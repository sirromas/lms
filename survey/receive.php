<?php

require_once './classes/Survey.php';
$survey = new Survey();
$item=$_REQUEST;
$list = $survey->send_survey_results($item);
echo $list;


