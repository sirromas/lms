<?php

require_once './classes/Utils.php';
$u = new Utils2();
$subs = $_POST['subs'];
$u->adjust_subs(json_decode($subs));
