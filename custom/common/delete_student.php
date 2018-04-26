<?php

require_once './Grades.php';
$gr = new Grades();
$students = $_REQUEST['students'];
$gr->delete_student($students);
