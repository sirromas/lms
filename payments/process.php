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

    $status = $pr->make_transaction($st_order);

    if ($status === false) {
        echo "<p align='center'>Transaction failed, please contact your bank for detailes.</p>";
    } else {
        $st_order->trans_id=$status['trans_id'];
        $st_order->auth_code=$status['auth_code'];
        $st_order->sum=$status['sum'];
        $orderStatus = $order->makeOrder($st_order);
        echo "<p align='center'>$orderStatus</p>";
    }
}





