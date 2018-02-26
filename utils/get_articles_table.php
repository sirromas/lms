<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$list = $u->get_publish_page();
echo $list;