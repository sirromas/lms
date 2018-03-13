<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$item = $_POST['item'];
$u->update_article_dates(json_decode($item));

