<?php

require_once './classes/Survey.php';
$s = new Survey();
$id = $_POST['id'];
$list = $s->update_camp_progress_data($id);
echo $list;
