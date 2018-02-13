<?php

require_once './Archive.php';
$ar   = new Archive();
$list = $ar->get_archive_page();
echo $list;