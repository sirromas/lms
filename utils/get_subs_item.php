<?php

require_once './classes/Utils.php';
$u = new Utils2;
$page = $_POST['id'];
$list = $u->get_subscritpion_item($page);
echo $list;
