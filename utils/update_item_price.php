<?php

require_once './classes/Utils.php';
$u = new Utils2();
$item = $_POST['item'];
$u->update_item_price(json_decode($item));
