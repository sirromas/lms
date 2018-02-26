<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$list = $u->get_online_classes_page();
echo $list;