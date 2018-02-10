<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$post = $_POST;
$file = $_FILES[0];
$u->upload_article_file( $file, $post );