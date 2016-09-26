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
    public $session;
    public $signup_url;
    public $courseid = 2;
    public $mail_smtp_host;
    public $mail_smtp_port;
    public $mail_smtp_user;
    public $mail_smtp_pwd;

    function __construct() {
        global $USER, $COURSE, $SESSION;
        $this->db = new pdo_db();
        $this->user = $USER;
        $this->course = $COURSE;
        $this->session = $SESSION;
        $this->signup_url = 'http://' . $_SERVER['SERVER_NAME'] . '/lms/login/mysignup.php';
        $this->mail_smtp_host = 'smtp.1and1.com';
        $this->mail_smtp_port = '25';
        $this->mail_smtp_user = 'lms@globalizationplus.com';
        $this->mail_smtp_pwd = 'aK6SKymc';
    }

    function is_group_exists($groupname) {
        $query = "select * from mdl_groups where name='$groupname'";
        $num = $this->db->numrows($query);
        return $num;
    }

    function is_email_exists($email) {
        $query = "select * from mdl_user where email='$email' and confirmed=1";
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
        $recipient = 'sirromas@gmail.com'; // temp workaround

        $mail->isSMTP();
        $mail->Host = $this->mail_smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $this->mail_smtp_user;
        $mail->Password = $this->mail_smtp_pwd;
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->mail_smtp_port;

        $mail->setFrom($this->mail_smtp_user, 'Globalization Plus');
        $mail->addAddress($recipient);
        $mail->addReplyTo($this->mail_smtp_user, 'Globalization Plus');

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $message;

        if (!$mail->send()) {
            $list = 'Mailer Error: ' . $mail->ErrorInfo . "\n";
        } // end if !$mail->send()
        
    }

}
