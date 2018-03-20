<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_REQUEST['item'];
$list = $gr->get_student_posts_details(json_decode($item));
echo $list;