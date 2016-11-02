<?php

require_once './classes/Utils.php';
$u = new Utils2();
$user = $_POST['user'];
$u->adjust_personal_trial_key(json_decode($user));
