<?php

require_once './classes/Navigation.php';
$url = $_POST['url'];
$nav = new Navigation();
$list = $nav->get_arcticle_content($url);
echo $list;
