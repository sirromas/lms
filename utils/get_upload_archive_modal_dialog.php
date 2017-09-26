<?php

require_once './classes/Utils.php';
$u = new Utils2();
$list = $u->get_upload_archive_modal_dialog();
echo $list;

