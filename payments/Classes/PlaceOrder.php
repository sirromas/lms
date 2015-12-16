<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/lib/moodlelib.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php');

class PlaceOrder {

    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }

    function makeOrder($order) {
        $query = "insert into mdl_order 
            (trans_id,
             auth_code,
             sum,
             name,
             address,
             city,
             state,
             zip,
             email,
             pay_type,
             date)
            VALUES ('" . mysql_real_escape_string($order->trans_id) . "',
                    '" . mysql_real_escape_string($order->auth_code) . "',
                    '" . mysql_real_escape_string($order->sum) . "',    
                    '" . mysql_real_escape_string($order->cds_name) . "',
                    '" . mysql_real_escape_string($order->cds_address_1) . "',
                    '" . mysql_real_escape_string($order->cds_city) . "',
                    '" . mysql_real_escape_string($order->cds_state) . "',
                    '" . mysql_real_escape_string($order->cds_zip) . "',
                    '" . mysql_real_escape_string($order->cds_email) . "',
                    '" . mysql_real_escape_string($order->cds_pay_type) . "',    
                    '" . mysql_real_escape_string(time()) . "')";
        $this->db->query($query);
        $status=$this->putEnrolKey(mysql_insert_id(), $order->cds_email);
        if ($status) {            
            $res="Thank you for your order. Confirmation email "
                    . "is sent to $order->cds_email."
                    . " If you did not receive confirmation email, please"
                    . " contact <a href='mailto:subscriptions@globalizationplus.com'>us</a>";
        }
        else {            
            $res="Thank you for your order. "
                    . "We could not send confirmation email to $order->cds_email."
                    . " Please contact "
                    . "<a href='mailto:subscriptions@globalizationplus.com'>us</a>"
                    . " to receive your enrol key";
        }        
        return $res;        
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
        $this->db->query($query);
        $status = $this->sendConfirmationEmail($email, $enrol_key, $exp_date);
        if ($status) {            
            return true;
        } else {            
            return false;
        }
    }

    function sendConfirmationEmail($email, $enrol_key, $exp_date) {
        $user = new stdClass();
        $from = 'Globalization Plus';
        $message = "";
        $subject = "Order Confirmation";
        $message_footer = "</body></html>";
        $message_header = "<html><body>";
        $message_body = "<p align='center'>Dear Customer!</p>"
                . "<p align='center'>Thank you for your order!</p>"
                . "<p>Your enrol key is: $enrol_key. It expires at " . date('Y-m-d', $exp_date) . "</p>"
                . "<p>If you still do not have Globalization Plus account "
                . "please be aware your enrol key is attached to your email $email "
                . "so please <a href='https://globalizationplus.com/registerteacher.html'>signup</a> with this email."
                . "If you have any issues please contact <a href='mailto:subscriptions@globalizationplus.com'>us</a></p>"
                . "<br/><br/><br/> <span>Globalization Plus Support team.</span>";
        $message = $message . $message_header;
        $message = $message . $message_body;
        $message = $message . $message_footer;

        $user->message = $message;
        $user->email = $email;
        $user->subject = $subject;
        $status=mail_to_user_payment($user, $from, $subject, $message);
        if ($status) {            
            return true;
        } else {            
            return false;
        }
    }

    function generateRandomString($length = 25) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

}
