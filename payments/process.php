<?php

require_once './Classes/PlaceOrder.php';
require_once './Classes/ProcessPayment.php';

$pr = new ProcessPayment();
$order = new PlaceOrder();

if ($_POST) {
    $st_order = new stdClass();
    foreach ($_POST as $key => $value) {
        $st_order->$key = $value;
    }
    $order->makeOrder($st_order);
}





