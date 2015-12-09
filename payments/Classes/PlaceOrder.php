<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/lib/moodlelib.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php');
require_once 'PHPMailerAutoload.php';

class PlaceOrder {

    private $db;

    function __construct() {
        $this->db = DB::getInstance();
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
        $this->db->query($query);
        $this->putEnrolKey(mysql_insert_id(), $order->cds_email);
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
        $this->sendConfirmationEmail($email, $enrol_key, $exp_date);
    }

    function sendConfirmationEmail($email, $enrol_key, $exp_date) {
        $message = "";
        $subject = "Order Confirmation";
        $message_footer = "</body></html>";
        $message_header = "<html><body>";
        $message_body = "<p align='center'>Dear Customer!</p>"
                . "<p align='center'>Thank you for your order!</p>"
                . "<p>Your enrol key is: $enrol_key. It expires at " . date('Y-m-d', $exp_date) . "</p>"
                . "<p><br/><br/>Best regards, Support team.</p>";
        $message = $message.$message_header;
        $message=$message.$message_body;
        $message=$message.$message_footer;          

        /*
        $mail = new PHPMailer();
        $mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.1and1.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'lms@globalizationplus.com';                 // SMTP username
        $mail->Password = 'aK6SKymc';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        $mail->setFrom('lms@globalizationplus.com', 'Mailer');
        $mail->addAddress($email);     // Add a recipient
        $mail->addReplyTo('lms@globalizationplus.com', 'Support team');
        $mail->isHTML(true);                                 
        $mail->Subject = $subject;
        $mail->Body = $message;
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {            
            echo "<br/>Thank you for your order. Confirmation email is sent to $email<br/>";
        }
        */
        $user=new stdClass();        
        email_to_user($user, 'lms@globalizationplus.com', $subject, $message_body, $message_header, $message_footer, $message, $email, 'lms@globalizationplus.com');
        
    }

    function generateRandomString($length = 25) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

}
