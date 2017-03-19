<?php

require_once './classes/Survey.php';
$s = new Survey();
$id = $_POST['id'];
$list = $s->get_survey_edit_page($id);
echo $list;
