<?php

require_once './classes/Survey.php';
$s = new Survey();
$list = $s->get_campaign_page();
echo $list;
