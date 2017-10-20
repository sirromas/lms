<?php

require_once './classes/Tutor.php';
$t = new Tutor();
$item = $_REQUEST['item'];
$list = $t->create_export_data(json_decode($item));
echo $list;