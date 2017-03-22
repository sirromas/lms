<?php

require_once './classes/Survey.php';
$s = new Survey();
$id = $_POST['id'];
$list = $s->get_chart_data($id);
echo $list;
