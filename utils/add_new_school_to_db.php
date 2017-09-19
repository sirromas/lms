<?php

require_once './classes/Utils.php';
$u = new Utils2();
$item = $_POST['item'];
$u->add_new_school_to_db(json_decode($item));

