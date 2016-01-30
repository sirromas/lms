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

}
