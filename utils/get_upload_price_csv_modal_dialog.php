<?php

require_once './classes/Utils.php';
$u = new Utils2();
$list = $u->get_upload_price_csv_modal_dialog();
echo $list;
