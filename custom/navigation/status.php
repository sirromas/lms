<?php

require_once './classes/Navigation.php';
$nav = new Navigation();
$list = $nav->is_logged();
echo $list;
