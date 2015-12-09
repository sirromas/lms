<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php');

/**
 * Description of PlaceOrder
 *
 * @author sirromas
 */
class PlaceOrder {

    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }

    function putEnrolKey($order_id, $email) {
        $exp_date = time() + 31536000; // one year later expiration date 
        $enrol_key = $this->generateRandomString();
        $query = "insert into mdl_enrol_key 
                    (order_id,
                     email,
                     enrol_key,
                     exp_date)
                     values(" . $order_id . ",
                        '" . mysql_real_escape_string($email) . "',
                        '" . mysql_real_escape_string($enrol_key) . "',    
                        '" . $exp_date . "')";
        echo "<br/>Query: $query<br/>";
        $this->db->query($query);
    }

    function sendConfirmationEmail($email) {
        echo "<br/>Thank you for your order. Confirmation email is sent to $email<br/>";
    }

    function generateRandomString($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    function makeOrder($order) {
        $query = "insert into mdl_order 
            (name,
             address,
             city,
             state,
             zip,
             email,
             pay_type,
             date)
            VALUES ('" . mysql_real_escape_string($order->cds_name) . "',
                    '" . mysql_real_escape_string($order->cds_address_1) . "',
                    '" . mysql_real_escape_string($order->cds_city) . "',
                    '" . mysql_real_escape_string($order->cds_state) . "',
                    '" . mysql_real_escape_string($order->cds_zip) . "',
                    '" . mysql_real_escape_string($order->cds_email) . "',
                    '" . mysql_real_escape_string($order->cds_pay_type) . "',    
                    '" . mysql_real_escape_string(time()) . "')";
        echo "<br/>Query: $query<br/>";
        $this->db->query($query);
        $this->putEnrolKey(mysql_insert_id(), $order->cds_email);
        $this->sendConfirmationEmail($order->cds_email);
    }

}
