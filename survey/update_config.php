<?php

require_once 'classes/Survey.php';
$survey = new Survey();
$config = $_POST['config'];
$list = $survey->update_config(json_decode($config));
echo $list;
