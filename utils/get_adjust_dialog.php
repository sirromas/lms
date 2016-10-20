<?php

require_once './classes/Utils.php';
$u = new Utils2();
$userid = $_POST['userid'];
$groupid = $_POST['groupid'];
$list = $u->get_adjust_dialog($userid, $groupid);
echo $list;
