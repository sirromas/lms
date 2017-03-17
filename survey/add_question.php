<?php

require_once './classes/Survey.php';
$s = new Survey();
$num = $_POST['num'];
$list = $s->get_questions_block($num);
echo $list;
