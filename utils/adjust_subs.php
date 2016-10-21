<?php

require_once './classes/Utils.php';
$u = new Utils2();
$userid = $_POST['userid'];
$groupid = $_POST['groupid'];
$start = $_POST['start'];
$exp = $_POST['exp'];
$u->adjust_subs($userid, $groupid, $start, $exp);
