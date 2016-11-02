<?php

require_once './classes/Utils.php';
$u = new Utils2();
$users = $_POST['users'];
$u->adjust_group_trial_keys($users);
