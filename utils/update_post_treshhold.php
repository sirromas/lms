<?php

require_once 'classes/Utils.php';
$u = new Utils2();
$period = $_REQUEST['period'];
$u->update_post_treshold($period);