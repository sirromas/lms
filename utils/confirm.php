<?php

require_once './classes/Utils.php';
$u = new Utils2();
$userid = $_POST['userid'];
$list = $u->confirm_tutor($userid);
echo $list;
