<?php

require_once 'classes/Utils.php';
$u = new Utils2;
$users = $_POST['users'];
$list = $u->get_group_modal_dialog($users);
echo $list;
