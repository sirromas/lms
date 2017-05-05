<?php

require_once './classes/Survey.php';
$s = new Survey();
$list = $s->upload_link_image($_FILES, $_POST);
echo $list;
