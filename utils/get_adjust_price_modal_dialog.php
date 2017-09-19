<?php

require_once './classes/Utils.php';
$u = new Utils2();
$id = $_POST['id'];
$list = $u->get_adjust_price_modal_dialog($id);
echo $list;


