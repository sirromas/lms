<?php

require_once './classes/Utils.php';
$u = new Utils2();
$list = $u->get_trial_keys_tab(false);
echo $list;
