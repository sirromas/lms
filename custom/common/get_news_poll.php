<?php

require_once './Quiz.php';
$q    = new Quiz();
$type = $_POST['type'];
$list = $q->get_poll_page( $type );
echo $list;