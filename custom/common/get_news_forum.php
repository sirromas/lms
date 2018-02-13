<?php

require_once './Forum.php';
$f      = new Forum();
$userid = $_POST['userid'];
$list   = $f->get_news_forum( $userid );
echo $list;
