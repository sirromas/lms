<?php

require_once './classes/Utils.php';
$u = new Utils2();
$id = $_POST['id'];
$u->delete_archive_article($id);