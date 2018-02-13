<?php

require_once './Export.php';
$ex     = new Export();
$userid = $_POST['userid'];
$list   = $ex->get_export_page( $userid );
echo $list;