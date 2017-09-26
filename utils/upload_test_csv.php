<?php
/**
 * Created by PhpStorm.
 * User: moyo
 * Date: 9/26/17
 * Time: 07:07
 */

require_once './classes/Utils.php';
$u=new Utils2();
$file = $_FILES[0];
$list = $u->upload_price_csv_data($file);
echo $list;
