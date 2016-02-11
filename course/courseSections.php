<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once ($_SERVER['DOCUMENT_ROOT'] . '/lms/class.pdo.database.php');

GLOBAL $COURSE, $USER;

class courseSections {

    private $db;
    private $context;
    private $courseid;
    private $userid;

    function __construct($context, $courseid, $userid) {
        $db = new pdo_db();
        $this->db = $db;
        $this->context = $context;
        $this->courseid = $courseid;
        $this->userid = $userid;
    }

    function getCourseRoles() {
        $roles = get_user_roles($this->context, $this->userid);
        if ($roles) {
            foreach ($roles as $item) {
                $id = $item->roleid;
            }
            return $id;
        }
    }

    function getForumId() {
        $query = "select * from mdl_course_modules where "
                . "course=" . $this->courseid . " and module=9";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            }
            return $id;
        }
    }

    function getPageId() {
        $query = "select * from mdl_course_modules where "
                . "course=" . $this->courseid . " and module=15 limit 0,1";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            }
            return $id;
        }
    }

    function getGlossaryId() {
        $query = "select * from mdl_course_modules where "
                . "course=" . $this->courseid . " and module=10 limit 0,1";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            }
            return $id;
        }
    }

    function getQuizId() {
        $query = "select * from mdl_course_modules where "
                . "course=" . $this->courseid . " and module=16 limit 0,1";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            }
            return $id;
        }
    }

    function remove_navigation_tutor_navigation_items($navbar, $item_to_remove) {
        $clean_navbar = str_ireplace($item_to_remove, '', $navbar);
        return $clean_navbar;
    }

    function get_group_secret_code() {
        $code = null;
        $tutor_groups = $this->getTutorGroups($this->userid);
        $groupid = end($tutor_groups);
        if ($groupid > 0) {
            $query = "select groupid, code from "
                    . "mdl_group_codes where groupid=$groupid";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $code = $row['code'];
                }
            } // end if $num > 0
        } // end if $groupid > 0
        return $code;
    }

    function getTutorGroups($userid) {
        $groups = array();
        $query = "select groupid, userid  "
                . "from mdl_groups_members "
                . "where userid=$userid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $groups[] = $row['groupid'];
            }
        }
        return $groups;
    }

    function verifyPromoCode($code) {
        $now = time();
        $query = "select code, active, expire_date "
                . "from mdl_promo_code "
                . "where active=1 and code='" .$code . "' "
                . "and expire_date>$now";
        //echo "Query: ".$query."<br/>";
        return $this->db->numrows($query);
    }

    function verifyPaidCode($email, $code) {
        $now = time();
        $query = "select email, enrol_key, exp_date "
                . "from mdl_enrol_key "
                . "where enrol_key='" . $code . "' "
                . "and email='" . $email . "' "
                . "and exp_date>$now";
        //echo "Query: ".$query."<br/>";
        return $this->db->numrows($query);
    }

    function checkEnrolKey($email, $code) {
        $promo_code = $this->verifyPromoCode($code);
        $paid_code = $this->verifyPaidCode($email, $code);
        if ($promo_code > 0 || $paid_code > 0) {
            $status = true;
        } // end if $promo_code>0 && $paid_code>0
        else {
            $status = false;
        }
        return $status;
    }

    function checkStudentEnrollKey($userid) {
        $query = "select id, email, enroll_key from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $enroll_key = $row['enroll_key'];
            $email = $row['email'];
        }
        if ($enroll_key == '') {
            $status = false;
        } // end if $enroll_key == ''
        else {
            $status = $this->checkEnrolKey($email, $enroll_key);
        }
        return $status;
    }

    function getStudentEnrollForm($userid) {
        $form = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

                <html xmlns='http://www.w3.org/1999/xhtml'>

                <head>
                    <script src='https://code.jquery.com/jquery-1.7.2.min.js'></script>
                    <script type='text/javascript' src='http://globalizationplus.com/lms/lms.js'></script>          
                    <link rel='stylesheet' href='../../register.css' />

                </head>

        <body>

        <br/><br/><br/> <p align='center'><img src='../../globalizationplus.jpg'></p>

                    <div class='wrapper clearfix'>
                <div align='center'>
                    <section class='userLogin userForm clearfix oneCol'>
                        <div class='loginForm dsR21'>

                            <div class='CSSTableGenerator' id='signupwrap'
                                 style='table-layout: fixed; width: 620px; align: center;'>

                                <form class='cmxform' id='enrol_form' method='post' action=''>
                                    <table>
                                        <tr>
                                            <td colspan='2'>ENROLL KEY</td>
                                        </tr>
                                        <tr>
                                            <td style='width: 250px;'><label for='key'>Key*</label></td>
                                            <input type='hidden' id='userid' name='userid' value='$userid'>
                                            <td><input id='key' name='key' 
                                                       style='background-color: rgb(250, 255, 189);width:173px;'/>&nbsp;<span
                                                       style='color: red; font-size: 12px;' id='key_err'></span></td>
                                        </tr>
                                        <tr>
                                        <td colspan='2'><input class='submit' type='submit' value='Submit'/></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>                   
                        </div>
                </div>
            </div>
         </body>
  </html>";
        return $form;
    }

}
