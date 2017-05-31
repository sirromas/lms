<?php

require_once './classes/Survey.php';
$s = new Survey();
$camp = $_POST['camp'];
$s->change_campaign_status(json_decode($camp));
