<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once ($_SERVER['DOCUMENT_ROOT'] . '/lms/moodle/class.pdo.database.php');

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
        foreach ($roles as $item) {
            $id = $item->roleid;
        }
        return $id;
    }

    function getForumId() {
        $query = "select * from mdl_course_modules where "
                . "course=" . $this->courseid . " and module=9";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }

    function getPageId() {
        $query = "select * from mdl_course_modules where "
                . "course=" . $this->courseid . " and module=15 limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }

    function getQuizId() {
        $query = "select * from mdl_course_modules where "
                . "course=" . $this->courseid . " and module=16 limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }
}
