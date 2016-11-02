<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/authorize/Payment.php';

class Student extends Utils {

    public $json_path;

    function __construct() {
        parent::__construct();
        $this->json_path = $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/students/groups.json';
    }

    function get_groups_list() {
        $groups = array();
        $query = "select * from mdl_groups order by name";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = mb_convert_encoding($row['name'], 'UTF-8');
        }
        file_put_contents($this->json_path, json_encode($groups));
    }

    function get_confirmation_message($userid, $groupid, $user) {
        $list = "";
        $class = $this->get_group_name($groupid);
        $payment_detailes = $this->get_payment_detailes($userid, $groupid);

        $list.="<html>";
        $list.="<body>";
        $list.="<br>";
        $list.="<p align='center'>Dear $user->firstname $user->lastname!</p>";
        $list.="<p align='center'>Thank you for signup.</p>";
        $list.="<table align='center'>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Username</td><td style='padding:15px'>$user->email</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Password</td><td style='padding:15px'>$user->pwd</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Class</td><td style='padding:15px'>$class</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Access key</td><td style='padding:15px'>$payment_detailes->auth_key</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Key validation period</td><td style='padding:15px'>From " . date('m/d/Y', $payment_detailes->start_date) . " to " . date('m/d/Y', $payment_detailes->exp_date) . "</td>";
        $list.="</tr>";

        $list.="</table>";

        $list.="<p align='center'>Best regards, <br> Globalization Plus Team.</p>";

        $list.="</body>";
        $list.="</html>";

        return $list;
    }

    function get_prolong_message($userid, $groupid, $user) {
        $list = "";
        $class = $this->get_group_name($groupid);
        $payment_detailes = $this->get_payment_detailes($userid, $groupid);

        $list.="<html>";
        $list.="<body>";
        $list.="<br>";
        $list.="<p align='center'>Dear $user->firstname $user->lastname!</p>";
        $list.="<p align='center'>Your subscription was renewed.</p>";
        $list.="<table align='center'>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Class</td><td style='padding:15px'>$class</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Access key</td><td style='padding:15px'>$payment_detailes->auth_key</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>Key validation period</td><td style='padding:15px'>From " . date('m/d/Y', $payment_detailes->start_date) . " to " . date('m/d/Y', $payment_detailes->exp_date) . "</td>";
        $list.="</tr>";

        $list.="</table>";

        $list.="<p align='center'>Best regards, <br> Globalization Plus Team.</p>";

        $list.="</body>";
        $list.="</html>";

        return $list;
    }

    function student_signup($user) {
        $list = "";
        $status = $this->signup($user); // $user is json encoded
        if ($status !== false) {
            $roleid = 5; // student
            $userobj = json_decode($user);
            $userid = $this->get_user_id($userobj->email);
            $groupid = $this->get_group_id($userobj->class);
            $this->enrol_user($userid, $roleid);
            $this->add_to_group($groupid, $userid);
            $p = new Payment();
            $result = $p->make_transaction(json_decode($user));
            if ($result !== false) {
                $subject = 'Signup confirmation';
                $message = $this->get_confirmation_message($userid, $groupid, $userobj);
                $email_status = $this->send_email($subject, $message, $userobj->email);
                if ($email_status === true) {
                    $list.="Thank you for signup! Confirmation email is sent to $userobj->email";
                } // end if
                else {
                    $list.="Signup is ok, but confirmation email was not sent. Please contact us by email <a href='mailto:info@globalizationplus.com'>info@globalizationplus.com</a>";
                } // end else
            } // end if
            else {
                $list.="Signup is ok, but payment is failed. Please contact us by email <a href='mailto:info@globalizationplus.com'>info@globalizationplus.com</a>Confirmation email is sent to $userobj->email";
            } // end else
        } // end if
        else {
            $list.="Signup error happened";
        }
        return $list;
    }

    function prolong_subscription($user) {
        $list = "";
        $userObj = json_decode($user);
        $user_data = $this->get_user_details($userObj->userid);
        $userObj->email = $user_data->email;
        $p = new Payment();
        $result = $p->make_transaction($userObj);
        if ($result !== false) {
            $subject = "Subscription Renew Confirmation";
            $message = $this->get_prolong_message($userObj->userid, $userObj->class, $userObj);
            $email_status = $this->send_email($subject, $message, $userObj->email);
            if ($email_status === true) {
                $list.="Payment is succesfull. Thank you! Confirmation email is sent to $userObj->email";
            } // end if
            else {
                $list.="Payment is succesfull, but confirmation email was not sent ($email_status). Please contact us by email <a href='mailto:info@globalizationplus.com'>info@globalizationplus.com</a>";
            } // end else
        } //  end if $result !== false
        else {
            $list.="Credit Card declined";
        }
        return $list;
    }

}
