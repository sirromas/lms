<?php

require_once './classes/Utils.php';
$u = new Utils2();
$files = $_FILES[0];
$data = $_POST;
$list = $u->upload_archive_article($files, $data);
echo $list;

