<?php

require_once './classes/Survey.php';
$survey = new Survey();
$title = $_POST['title'];
$email = $_POST['email'];
$result = $_POST['result'];
$list = $survey->send_survey_results($title, $email, $result);
echo $list;
