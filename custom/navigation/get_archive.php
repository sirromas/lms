<?php

require_once './classes/Navigation.php';
$nav = new Navigation();
$url = $_POST['url'];
$list = $nav->get_archive_page($url);
echo $list;
