<?php

require_once './classes/Student.php';
$st = new Student();
$link = $st->get_article_page();
echo $link;