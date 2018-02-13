<?php

require_once './Grades.php';
$gr   = new Grades();
$item = $_POST['item'];
$list = $item->get_csv_student_grades( json_decode( $item ) );
echo $list;

