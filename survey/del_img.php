<?php

require_once './classes/Survey.php';
$s = new Survey();
$id = $_POST['id'];
$s->del_answer_image($id);
