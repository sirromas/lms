<?php

require_once './classes/Survey.php';
$s = new Survey();
$camp = $_POST['camp'];
$list = $s->update_camp(json_decode($camp));
echo $list;
