<?php

require_once './classes/Student.php';
$st = new Student();
$name = trim($_POST['name']);
$price = $st->get_school_price($name);
echo $price;
