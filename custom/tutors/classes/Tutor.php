<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Tutor extends Utils {

    function __construct() {
        parent::__construct();
    }

    function create_group($groupname) {
        $query = "insert into mdl_groups "
                . "(courseid,idnumber,name) "
                . "values($this->courseid,"
                . " ' ',"
                . " '" . $groupname . "')";
        $this->db->query($query);

        $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
        $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
        $lastId = $lastid_arr[0];

        return $lastId;
    }

    function tutor_signup($user) {
        $list = "";
        $result = $this->signup($user);
        if ($result !== false) {
            $roleid = 4; // non-editing teacher
            $userObj = json_decode($user);
            $groupname = $userObj->class;
            $email = $userObj->email;
            $userid = $this->get_user_id($email);
            $this->enrol_user($userid, $roleid);
            $groupid = $this->create_group($groupname);
            $this->add_to_group($groupid, $userid);
            $this->send_tutor_confirmation_email($userObj);
            $list.="Thank you for signup. Confirmation email is sent to $userObj->email .";
        } // end if $result!==false
        else {
            $list.="Signup error happened.ß";
        } // end else 
        return $list;
    }

    function send_tutor_confirmation_email($user) {
        $subject = 'Signup confirmation';
        $msg = "";

        $msg.="<!DOCTYPE html>

        <html>
            <head>
                <title>Signup confirmation</title>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width = device-width, initial-scale = 1.0'>

        </head>
        <body>
            <br>
            <p align='center'>Dear $user->firstname $user->lastname! </p>
            <p align='center'>Thank you for signup!</p> 
            <table align='center'>
            <tr>
            <td>Username:</td><td>$user->email</td>
            </tr>
            <tr>
            <td>Password</td><td>$user->pwd</td>
            </tr>
            <tr>
            <td>Class name:</td><td>$user->class</td>
            </tr>
            <tr>
            <td colspan='2'>With best regards, Globalization plus team</td>
            </tr>
            </table>
        </body>
        </html>";
        $this->send_email($subject, $msg, $user->email);
    }

}
