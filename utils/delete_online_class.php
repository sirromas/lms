<?php

require_once './classes/Utils.php';
$u  = new Utils2();
$id = $_REQUEST['id'];
$u->delete_online_class($id);
