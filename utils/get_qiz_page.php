<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$type = $_POST['type'];
$list = $u->get_news_quiz_page();
echo $list;
