<?php

require_once './classes/Utils.php';
$u = new Utils2();
$item = $_REQUEST['item'];
$u->update_from_email(json_decode($item));
