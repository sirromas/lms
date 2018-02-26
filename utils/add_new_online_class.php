<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$item = $_REQUEST['item'];
$u->add_new_online_class(json_decode($item));
