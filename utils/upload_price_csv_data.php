<?php

require_once './classes/Utils.php';
$u = new Utils2();
$file = $_FILES[0];
$list = $u->upload_price_csv_data($file);
echo $list;
