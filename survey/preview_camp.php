<?php

require_once './classes/Survey.php';
$s = new Survey();
$id = $_POST['id'];
$list = $s->preview_campaign($id);
echo $list;

