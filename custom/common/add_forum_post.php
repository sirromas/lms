<?php

require_once './Forum.php';
$f    = new Forum();
$item = $_POST['item'];
$f->add_forum_post( json_decode( $item ) );