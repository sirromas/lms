<?php

require_once './classes/Utils.php';
$u = new Utils2();
$list = $u->get_subscription_list(false);
echo $list;

