<?php

require_once './classes/Survey.php';
$survey = new Survey();
$email = $_REQUEST['email'];
$id = $_REQUEST['id'];
$list = $survey->send_survey_results($email, $id);
echo $list;


