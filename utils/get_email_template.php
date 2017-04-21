<?php

require_once './classes/Utils.php';
$u = new Utils2();
$id = $_POST['id'];
$list = $u->get_email_template($id);
echo $list;


