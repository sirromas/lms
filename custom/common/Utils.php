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
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/postmark/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/pdf/mpdf/mpdf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/51/core/51Degrees.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/51/core/51Degrees_usage.php';

use Postmark\PostmarkClient;

class Utils
{

    public $db;
    public $user;
    public $course;
    public $group;
    public $session;
    public $signup_url;
    public $courseid;
    public $from;
    const COURSE_ID = 2;

    /**
     * @return mixed
     */
    function get_actual_course_id()
    {
        $query = "select * from mdl_course where category<>0 "
            . "order by id desc limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }

        return $id;
    }

    /**
     * Utils constructor.
     */
    function __construct()
    {
        global $USER, $COURSE, $GROUP, $SESSION;
        $this->db = new pdo_db();
        $this->user = $USER;
        $this->course = $COURSE;
        $this->group = $GROUP;
        $this->session = $SESSION;
        $this->signup_url = 'https://www.' . $_SERVER['SERVER_NAME'] . '/lms/login/mysignup.php';
        $this->from = 'info@newsfactsandanalysis.com';
        $this->courseid = $this->get_actual_course_id();
    }

    /**
     * @param $groupname
     *
     * @return int
     */
    function is_group_exists($groupname)
    {
        $query = "select * from mdl_groups where name='$groupname'";
        $num = $this->db->numrows($query);

        return $num;
    }

    /**
     * @param $email
     *
     * @return int
     */
    function is_email_exists($email)
    {
        $query = "select * from mdl_user where email='$email' "
            . "and confirmed=1 and deleted=0";
        $num = $this->db->numrows($query);

        return $num;
    }

    /**
     * @param $userObj
     *
     * @return bool|string
     */
    function signup($userObj)
    {
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

        //return $response;
        return 1;
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    function get_user_id($email)
    {
        $query = "select * from mdl_user where email='$email'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }

        return $id;
    }

    /**
     * @return mixed
     */
    function get_course_context()
    {
        $query = "select id from mdl_context
                     where contextlevel=50
                     and instanceid='" . $this->courseid . "' ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $contextid = $row['id'];
        }

        return $contextid;
    }

    /**
     * @return mixed
     */
    function get_enrol_id()
    {
        $query = "select id from mdl_enrol
                     where courseid=" . $this->courseid . " and enrol='self'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $enrolid = $row['id'];
        }

        return $enrolid;
    }

    /**
     * @param $userid
     * @param $roleid
     */
    function enrol_user($userid, $roleid)
    {
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
        //echo "Query1: ".$query."<br>";
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
        //echo "Query2: ".$query."<br>";
        $this->db->query($query);
    }

    /**
     * @param $groupid
     * @param $userid
     */
    function add_to_group($groupid, $userid)
    {
        groups_add_member($groupid, $userid);
    }

    /**
     *
     */
    function sample_send()
    {
        $client = new PostmarkClient("5a470ceb-d8d6-49cb-911c-55cbaeec199f");

        $sendResult = $client->sendEmail(
            "info@atic.kiev.ua", "sirromas@gmail.com", "Hello from Postmark!", "This is just a friendly 'hello' from your friends at Postmark."
        );
    }

    /**
     * @param $subject
     * @param $message
     * @param $recipient
     *
     * @return bool
     */
    function send_email($subject, $message, $recipient, $send_copy = true, $from = 'info@newsfactsandanalysis.com')
    {
        $recipientA = 'sirromas@gmail.com'; // copy should be sent to me
        $recipientB = 'steve@posnermail.com'; // copy should be sent to Steve
        $client = new PostmarkClient("8160e35c-3fb3-4e2e-b73d-e0fb76c9da34"); // Steve Postmark server?
        $client->sendEmail($from, $recipient, $subject, $message);
        $client->sendEmail($from, $recipientA, $subject, $message);
        if ($send_copy) {
            $client->sendEmail($from, $recipientB, $subject, $message);
        }

        return true;
    }


    /**
     * @param $roleid
     * @return string
     */
    function get_from_address($roleid)
    {
        $query = "select * from mdl_from_addr where roleid=$roleid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $email = $row['email'];
            }
        } // end if
        else {
            $email = 'info@newsfactsandanalysis.com';
        } // end else
        return $email;
    }

    /**
     * @param $courseid
     */
    function get_course_modules($courseid)
    {
        $modules = array();
        $query = "select * from mdl_course_modules where course=$courseid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $modules[] = $row['module'];
            } // end while
        } // end if $num > 0
        return $modules;
    }

    /**
     * @param $groupname
     *
     * @return mixed
     */
    function get_group_id($groupname)
    {
        $query = "select * from mdl_groups where name='$groupname'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }

        return $id;
    }

    /**
     * @param $userid
     *
     * @return stdClass
     */
    function get_user_details($userid)
    {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $user = new stdClass();
            foreach ($row as $key => $value) {
                $user->$key = $value;
            }
        }

        return $user;
    }

    /**
     * @param $userid
     * @param $groupid
     *
     * @return stdClass
     */
    function get_payment_detailes($userid, $groupid)
    {
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

    /**
     * @param $userid
     * @return bool
     */
    function is_valid_user($userid)
    {
        $status = false;
        $query = "select * from mdl_user where id=$userid and deleted=0";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $email = $row['email'];
            if ($firstname != '' && $lastname != '' && $email != '') {
                $status = true;
            }
        }
        return $status;
    }

    /**
     * @param $groupid
     * @param $userid
     */
    function delete_from_group($groupid, $userid)
    {
        $query = "delete from mdl_groups_members 
                where groupid=$groupid and userid=$userid";
        echo "Query: " . $query . "<br>";
        $this->db->query($query);
    }


    /**
     * @param $groupid
     * @param $userid
     */
    function add_student_to_group($groupid, $userid)
    {
        $now = time();
        $query = "insert into mdl_groups_members 
                (groupid, userid, timeadded) 
                values ($groupid,$userid,'$now')";
        echo "Move Query: " . $query . "<br>";
        $this->db->query($query);
    }

    /**
     * @param $groupid
     *
     * @return array
     */
    function get_group_users($groupid, $roleid = 0)
    {
        $query = "select * from mdl_groups_members where groupid=$groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $status = $this->is_valid_user($row['userid']);
            if ($status) {
                if ($roleid > 0) {
                    $user_role = $this->get_user_role_by_id($row['userid']);
                    if ($roleid == $user_role) {
                        $users[] = $row['userid'];
                    }
                } else {
                    $users[] = $row['userid'];
                }
            }
        }

        return $users;
    }


    /**
     * @param $userid
     *
     * @return bool
     */
    function is_teacher($userid)
    {
        $query = "select * from mdl_role_assignments where userid=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $roleid = $row['roleid'];
        }
        $status = ($roleid == 4) ? true : false;

        return $status;
    }

    /**
     * @param $groupid
     *
     * @return mixed
     */
    function get_group_teacher($groupid)
    {
        $group_users = $this->get_group_users($groupid);
        foreach ($group_users as $userid) {
            $status = $this->is_teacher($userid);
            if ($status) {
                return $userid;
            } // end if status
        } // end foreach
    }

    /**
     * @param $teacherid
     *
     * @return mixed
     */
    function get_school_name($teacherid)
    {
        $query = "select * from mdl_user where id=$teacherid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $class = $row['institution'];
        }

        return $class;
    }

    /**
     * @param $groupid
     *
     * @return mixed
     */
    function get_group_name($groupid)
    {
        $query = "select * from mdl_groups where id=$groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }

        return $name;
    }

    /**
     * @param int $length
     *
     * @return bool|string
     */
    function generateRandomString($length = 25)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * @return int
     */
    function get_user_role()
    {
        $contextid = $this->get_course_context();
        $userid = $this->user->id;
        if ($userid != 2) {
            $query = "select * from mdl_role_assignments "
                . "where contextid=$contextid "
                . "and userid=$userid";
            //echo "Query: " . $query . "<br>";
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

    /**
     * @param $userid
     * @return int
     */
    function get_user_role_by_id($userid)
    {
        $contextid = $this->get_course_context();
        if ($userid != 2) {
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

    /**
     * @return array
     */
    function get_user_groups()
    {
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

    /**
     * @param $userid
     * @return array
     */
    function get_user_groups_by_userid($userid)
    {
        $groups = array();
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

    /**
     * @param $email
     *
     * @return int
     */
    function is_user_student($email)
    {
        $userid = $this->get_user_id($email);
        $roleid = $this->get_user_role();

        return $roleid;
    }

    /**
     * @param $userid
     */
    function is_student_has_key($userid)
    {
        // 1. Check among card payments table mdl_card_payments
        // 2. Check among trial keys table mdl_trial_keys

        $query = "select * from mdl_card_payments where userid=$userid";
        $num = $this->db->numrows($query);
        if ($num > 0) {

        } // end if $num>0
        else {

        } // end else
    }

    /**
     * @return string
     */
    function get_article_page()
    {
        $query = "select * from mdl_archive order by adate desc limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $link = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/utils/archive/" . $row['path'] . "";
        }

        return $link;
    }

    /**
     * @return mixed
     */
    function get_news_id()
    {
        $now = time();
        $query = "select * from mdl_article where $now between start and expire order by id desc limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $aid = $row['id'];
        }

        return $aid;
    }


    /**
     * @param $userobj
     * @param $userid
     */
    function set_user_password($userobj, $userid)
    {
        $query = "update mdl_user set purepwd='$userobj->pwd' where id=$userid";
        $this->db->query($query);
    }

}
