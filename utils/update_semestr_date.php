<?php

require_once 'classes/Utils.php';
$u = new Utils2();
$item = $_REQUEST['item'];
$u->update_semestr_date(json_decode($item));
