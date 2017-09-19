<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Tutor extends Utils {

    function __construct() {
        parent::__construct();
    }

    function create_group($groupname) {
        if ($groupname != '') {
            $status = $this->is_group_exists($groupname);
            if ($status == 0) {
                $query = "insert into mdl_groups "
                        . "(courseid,idnumber,name) "
                        . "values($this->courseid,"
                        . " ' ',"
                        . " '" . $groupname . "')";
                $this->db->query($query);
                $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
                $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
                $lastId = $lastid_arr[0];
            } // end if $status==0
            else {
                $lastId = 0;
            }
        } // end if $groupname!=''
        else {
            $lastId = 0;
        }
        return $lastId;
    }

    function confirm_tutor($user) {
        $query = "update mdl_user set policyagreed='1' where email='$user->email'";
        $this->db->query($query);
    }

    function tutor_signup($user) {
        $list = "";
        $groups = array();
        $result = $this->signup($user);
        if ($result !== false) {
            $roleid = 4; // non-editing teacher
            $userObj = json_decode($user);
            $email = $userObj->email;
            $userid = $this->get_user_id($email);
            $userObj->userid = $userid;
            $this->enrol_user($userid, $roleid);

            $course1 = $userObj->course1;
            $groupid = $this->create_group($course1);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course2 = $userObj->course2;
            $groupid = $this->create_group($course2);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course3 = $userObj->course3;
            $groupid = $this->create_group($course3);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course4 = $userObj->course4;
            $groupid = $this->create_group($course4);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course5 = $userObj->course5;
            $groupid = $this->create_group($course5);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course6 = $userObj->course6;
            $groupid = $this->create_group($course6);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $this->confirm_tutor($userObj);
            $userObj->confirmed = 1;
            $userObj->confirmed = 1;
            $userObj->groups = $groups;
            $this->send_tutor_confirmation_email($userObj);
            $list.="Thank you for signup. Confirmation email is sent to $userObj->email .";
        } // end if $result!==false
        else {
            $list.="Signup error happened";
        } // end else 
        return $list;
    }

    function verify_tutor($user, $output = TRUE) {
        $list = "";
        $page = file_get_contents($user->url);
        $status1 = strstr($page, $user->email);
        $status2 = strstr($page, $user->username);
        if ($status1 !== FALSE && $status2 !== FALSE) {
            $query = "update mdl_user set policyagreed='1' where email='$user->email'";
            $this->db->query($query);
            if ($output) {
                $list.="Thank you. Your membership is confirmed";
            } // end if
            else {
                return TRUE;
            }
        } // end if
        else {
            if ($output) {
                $list.="Your membership was not confirmed";
            } // end if 
            else {
                return FALSE;
            } // end else
        } // end else
        return $list;
    }

    function send_non_confirmed_tutor_notification($user) {
        $msg = "";
        $msg.="<html>";
        $msg.="<body>";

        $msg.="<p>Non-confirmed professor's registration:</p>";

        $msg.="<table>";

        $msg.="<tr>";
        $msg.="<td style='padding:15px;'>First name</td><td style='padding:15px;'>$user->firstname</td>";
        $msg.="</tr>";

        $msg.="<tr>";
        $msg.="<td style='padding:15px;'>Last name</td><td style='padding:15px;'>$user->lastname</td>";
        $msg.="</tr>";

        $msg.="<tr>";
        $msg.="<td style='padding:15px;'>Email</td><td style='padding:15px;'>$user->email</td>";
        $msg.="</tr>";

        $msg.="<tr>";
        $msg.="<td style='padding:15px;'>Phone</td><td style='padding:15px;'>$user->phone</td>";
        $msg.="</tr>";

        $msg.="</table>";
        $msg.="</body>";
        $msg.="</html>";

        $subject = "Non-confirmed professor's registration";
        $recipientA = 'sirromas@gmail.com';
        $recipientB = 'steve@posnermail.com ';
        $this->send_email($subject, $msg, $recipientA);
        $this->send_email($subject, $msg, $recipientB);
    }

    function get_tutor_classes_signup_links($user) {
        $list = "";
        $groups = $user->groups;
        if (count($groups) > 0) {
            foreach ($groups as $id) {
                $name = $this->get_group_name($id);
                $list.="<p><a href='http://www." . $_SERVER['SERVER_NAME'] . "/registerstudentbody.html?groupid=$id' target='_blank'>$name</a></p>";
            } // end foreach
        } // end if count($groups)>0

        return $list;
    }

    function get_tutor_classes($user) {
        $list = "";
        $groups = $user->groups;
        if (count($groups) > 0) {
            foreach ($groups as $id) {
                $name = $this->get_group_name($id);
                $list.="<p>$name</p>";
            } // end foreach
        } // end if count($groups)>0
        return $list;
    }

    function get_tutor_confirmation_message($user) {
        if ($user->confirmed == 0) {
            $query = "select * from mdl_email_templates "
                    . "where template_name='tutor_non_confirmed'";
        } // end if 
        else {
            $query = "select * from mdl_email_templates "
                    . "where template_name='tutor_confirmed'";
        } // end else

        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $content = $row['template_content'];
        }

        $classes = $this->get_tutor_classes($user);
        $links = $this->get_tutor_classes_signup_links($user);
        $search = array('{firstname}', '{lastname}', '{email}', '{password}', '{class}', '{links}');
        $replace = array($user->firstname, $user->lastname, $user->email, $user->pwd, $classes, $links);
        $message = str_replace($search, $replace, $content);
        return $message;
    }

    function send_tutor_confirmation_email($user) {
        $subject = 'Signup confirmation';
        $msg = "";
        $msg.=$this->get_tutor_confirmation_message($user);
        $result = $this->send_email($subject, $msg, $user->email);
        return $result;
    }

}
