<?php

require_once './classes/Navigation.php';
$nav = new Navigation();
$list = $nav->get_dictionary_content();
echo $list;
