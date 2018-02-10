<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$list = $u->get_news_forum_page();
echo $list;