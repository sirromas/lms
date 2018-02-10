<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$item = $_POST['item'];
$list = $u->add_new_forum( json_decode( $item ) );
echo $list;

