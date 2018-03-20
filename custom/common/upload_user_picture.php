<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_REQUEST['item'];
$gr->upload_user_picture(json_decode($item));