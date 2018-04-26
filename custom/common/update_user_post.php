<?php

require_once './Forum.php';
$f = new Forum();
$item = $_REQUEST['item'];
$f->update_user_post(json_decode($item));
