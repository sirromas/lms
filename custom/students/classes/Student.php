<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/authorize/Payment.php';

class Student extends Utils
{

    public $json_path;
    public $univesrity_path;

    function __construct()
    {
        parent::__construct();
        $this->json_path = $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/students/groups.json';
        $this->univesrity_path = $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/students/un.json';
    }

    function get_groups_list()
    {
        $groups = array();
        $query = "select * from mdl_groups order by name";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = mb_convert_encoding($row['name'], 'UTF-8');
        }
        file_put_contents($this->json_path, json_encode($groups));
        echo "Groups data are updated ....";
    }

    function create_univsersity_data()
    {
        $un = array();
        $query = "select * from mdl_price where id>1 and price<>0";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $un[] = mb_convert_encoding($row['institute'], 'UTF-8');
        }
        $clear_data = array_unique($un);
        file_put_contents($this->univesrity_path, json_encode($clear_data));
        echo "University data are updated ....";
    }

    function get_class_data($groupid)
    {
        $teacherid = $this->get_group_teacher($groupid);
        $school = $this->get_school_name($teacherid);
        $class = $this->get_group_name($groupid);
        $price = $this->get_school_price($school);
        $data = array('school' => $school, 'name' => $class, 'price' => $price);
        return json_encode($data);
    }

    function get_student_message_from_template($user, $class, $payment_detailes)
    {
        $date1 = date('m/d/Y', $payment_detailes->start_date);
        $date2 = date('m/d/Y', $payment_detailes->exp_date);
        $query = "select * from mdl_email_templates where template_name='student'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $content = $row['template_content'];
        }
        $search = array('{firstname}', '{lastname}', '{email}', '{password}', '{class}', '{key}', '{date1}', '{date2}');
        $replace = array($user->firstname, $user->lastname, $user->email, $user->pwd, $class, $payment_detailes->auth_key, $date1, $date2);
        $message = str_replace($search, $replace, $content);
        return $message;
    }

    function get_confirmation_message($userid, $groupid, $user)
    {
        $list = "";
        $class = $this->get_group_name($groupid);
        $payment_detailes = $this->get_payment_detailes($userid, $groupid);
        $list .= $this->get_student_message_from_template($user, $class, $payment_detailes);
        return $list;
    }

    function get_prolong_message($userid, $groupid, $user)
    {
        $list = "";
        $class = $this->get_group_name($groupid);
        $payment_detailes = $this->get_payment_detailes($userid, $groupid);

        $list .= "<html>";
        $list .= "<body>";
        $list .= "<br>";
        $list .= "<p align='center'>Dear $user->firstname $user->lastname!</p>";
        $list .= "<p align='center'>Your subscription was renewed.</p>";
        $list .= "<table align='center'>";

        $list .= "<tr>";
        $list .= "<td style='padding:15px'>Class</td><td style='padding:15px'>$class</td>";
        $list .= "</tr>";

        $list .= "<tr>";
        $list .= "<td style='padding:15px'>Access key</td><td style='padding:15px'>$payment_detailes->auth_key</td>";
        $list .= "</tr>";

        $list .= "<tr>";
        $list .= "<td style='padding:15px'>Key validation period</td><td style='padding:15px'>From " . date('m/d/Y', $payment_detailes->start_date) . " to " . date('m/d/Y', $payment_detailes->exp_date) . "</td>";
        $list .= "</tr>";

        $list .= "</table>";

        $list .= "<p align='center'>Best regards, <br> Globalization Plus Team.</p>";

        $list .= "</body>";
        $list .= "</html>";

        return $list;
    }

    function delete_user_registration($userid)
    {
        $query = "delete mdl_user where id=$userid";
        $this->db->query($query);
    }

    function student_signup($user)
    {
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
                $list .= "Thank you for signup! Confirmation email is sent to $userobj->email";
            } // end if $result !== false
            else {
                $this->delete_user_registration($userid);
                $list .= "Credit card declined";
            } // end else
        } // end if $status !== false
        else {
            $list .= "Signup error happened";
        }
        return $list;
    }

    function prolong_subscription($user)
    {
        $list = "";
        $userObj = json_decode($user);
        $userObj->cardholder = $userObj->firstname . ' ' . $userObj->lastname;

        /*
          echo "<pre>";
          print_r($userObj);
          echo "</pre>";
         */

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
            $list .= "<p align='center'>Payment is succesfull. Thank you! <br> Your key is $key , expiration date $exp</p>";
        } //  end if $result !== false
        else {
            $list .= "Credit Card Declined";
        }
        return $list;
    }

    function get_basic_price($id)
    {
        $query = "select * from mdl_price where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $amount = $row['price'];
        }
        return $amount;
    }

    function get_school_price($name)
    {
        $query = "select * from mdl_price where institute='$name'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $amount = $row['price'];
        }
        return $amount;
    }

}
