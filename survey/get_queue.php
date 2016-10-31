<?php

require_once './classes/Survey.php';
$s = new Survey();
$list = $s->get_queue_status();
echo $list;
