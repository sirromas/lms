<?php

require_once './classes/Student.php';
$st = new Student();
$id = $_POST['id'];
$price = $st->get_basic_price($id);
echo $price;
