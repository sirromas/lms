<?php

require_once './classes/Utils.php';
$u = new Utils2();
$user = $_POST['user'];
$list = $u->get_adjust_trial_personal_key_modal_dialog(json_decode($user));
echo $list;
