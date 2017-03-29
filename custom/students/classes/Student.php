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
        echo "Groups data are updated ....";
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
                $this->send_email($subject, $message, $userobj->email);
                $list.="Thank you for signup! Confirmation email is sent to $userobj->email";
            } // end if $result !== false
            else {
                $list.="Signup error happened";
            } // end else
        } // end if $status !== false
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
            $key = $result->key;
            $exp = $result->e;
            $subject = "Subscription Renew Confirmation";
            $message = $this->get_prolong_message($userObj->userid, $userObj->class, $userObj);
            $this->send_email($subject, $message, $userObj->email);
            $list.="<p align='center'>Payment is succesfull. Thank you! <br> Your key is $key , expiration date $exp</p>";
        } //  end if $result !== false
        else {
            $list.="Credit Card Declined";
        }
        return $list;
    }

}
