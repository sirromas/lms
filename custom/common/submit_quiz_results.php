<?php

require_once './Quiz.php';
$q    = new Quiz();
$item = $_POST['item'];
$list = $q->submit_quiz_results( json_decode( $item ) );
echo $list;