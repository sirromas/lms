<?php

require_once './classes/Utils.php';
$u = new Utils2();
$item = $_POST['item'];
$list = $u->search_tutor(trim($item));
echo $list;
