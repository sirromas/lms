<?php

/**
 * Description of Utils
 *
 * @author moyo
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/group/lib.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/mailer/vendor/PHPMailerAutoload.php';

class Utils {

    public $db;
    public $user;
    public $course;
    public $group;
    public $session;
    public $signup_url;
    public $courseid = 2;
    public $mail_smtp_host;
    public $mail_smtp_port;
    public $mail_smtp_user;
    public $mail_smtp_pwd;

    function __construct() {
        global $USER, $COURSE, $GROUP, $SESSION;
        $this->db = new pdo_db();
        $this->user = $USER;
        $this->course = $COURSE;
        $this->group = $GROUP;
        $this->session = $SESSION;
        $this->signup_url = 'http://' . $_SERVER['SERVER_NAME'] . '/lms/login/mysignup.php';
        $this->mail_smtp_host = 'smtp.1and1.com';
        $this->mail_smtp_port = '25';
        $this->mail_smtp_user = 'info@globalizationplus.com';
        $this->mail_smtp_pwd = 'abba1abba2';
    }

    function is_group_exists($groupname) {
        $query = "select * from mdl_groups where name='$groupname'";
        $num = $this->db->numrows($query);
        return $num;
    }

    function is_email_exists($email) {
        $query = "select * from mdl_user where email='$email' "
                . "and confirmed=1 and deleted=0";
        $num = $this->db->numrows($query);
        return $num;
    }

    function signup($userObj) {
        $data = array('user' => $userObj); // JSON encoded user

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $response = @file_get_contents($this->signup_url, false, $context);
        return $response;
    }

    function get_user_id($email) {
        $query = "select * from mdl_user where email='$email'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }

    function get_course_context() {
        $query = "select id from mdl_context
                     where contextlevel=50
                     and instanceid='" . $this->courseid . "' ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $contextid = $row['id'];
        }
        return $contextid;
    }

    function get_enrol_id() {
        $query = "select id from mdl_enrol
                     where courseid=" . $this->courseid . " and enrol='self'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $enrolid = $row['id'];
        }
        return $enrolid;
    }

    function enrol_user($userid, $roleid) {
        $enrolid = $this->get_enrol_id();
        $contextid = $this->get_course_context();
        // 1. Insert into mdl_user_enrolments table
        $query = "insert into mdl_user_enrolments
             (enrolid,
              userid,
              timestart,
              modifierid,
              timecreated,
              timemodified)
               values ('" . $enrolid . "',
                       '" . $userid . "',
                        '" . time() . "',   
                        '2',
                         '" . time() . "',
                         '" . time() . "')";
        $this->db->query($query);

        // 2. Insert into mdl_role_assignments table
        $query = "insert into mdl_role_assignments
                  (roleid,
                   contextid,
                   userid,
                   timemodified,
                   modifierid)                   
                   values ('" . $roleid . "',
                           '" . $contextid . "',
                           '" . $userid . "',
                           '" . time() . "',
                            '2'         )";
        $this->db->query($query);
    }

    function add_to_group($groupid, $userid) {
        groups_add_member($groupid, $userid);
    }

    function send_email($subject, $message, $recipient) {

        $mail = new PHPMailer;
        $recipientA = 'sirromas@gmail.com'; // temp workaround
        //$mail->SMTPDebug = 2;

        $mail->isSMTP();
        $mail->Host = $this->mail_smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $this->mail_smtp_user;
        $mail->Password = $this->mail_smtp_pwd;
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->mail_smtp_port;

        $mail->setFrom($this->mail_smtp_user, 'Globalization Plus');
        $mail->addAddress($recipientA);
        $mail->addAddress($recipient);
        $mail->addReplyTo($this->mail_smtp_user, 'Globalization Plus');

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $message;

        if (!$mail->send()) {
            $list = 'Mailer Error: ' . $mail->ErrorInfo . "\n";
            return $list;
        } // end if !$mail->send()
        else {
            return true;
        }
    }

    function get_group_id($groupname) {
        $query = "select * from mdl_groups where name='$groupname'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }

    function get_user_details($userid) {
        $query = "select * from mdl_user where id=$userid";
        //echo "Query: ".$query."<br>";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $user = new stdClass();
            foreach ($row as $key => $value) {
                $user->$key = $value;
            }
        }
        return $user;
    }

    function get_payment_detailes($userid, $groupid) {
        $query = "select * from mdl_card_payments "
                . "where userid=$userid and groupid=$groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $payment = new stdClass();
            foreach ($row as $key => $value) {
                $payment->$key = $value;
            }
        }
        return $payment;
    }

    function get_group_name($groupid) {
        $query = "select * from mdl_groups where id=$groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }
        return $name;
    }

    function generateRandomString($length = 25) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    function get_user_role() {
        $contextid = $this->get_course_context();
        $userid = $this->user->id;
        if ($userid != 2 && $userid != 3) {
            $query = "select * from mdl_role_assignments "
                    . "where contextid=$contextid "
                    . "and userid=$userid";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $roleid = $row['roleid'];
            }
        } // end if
        else {
            $roleid = 0; // Admin & Manager
        } // end else
        return $roleid;
    }

    function get_user_groups() {
        $groups = array();
        $userid = $this->user->id;
        $query = "select * from mdl_groups_members where userid=$userid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $groups[] = $row['groupid'];
            }
        } // end if
        return $groups;
    }

}
