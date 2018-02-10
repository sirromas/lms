<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$item = $_POST['item'];
$list = $u->get_quiz_page_step2( json_decode( $item ) );
echo $list;
