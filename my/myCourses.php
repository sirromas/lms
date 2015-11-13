<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once ($_SERVER['DOCUMENT_ROOT'] . '/lms/moodle/class.pdo.database.php');

class myCourses {

    private $db;
    private $userid;

    function __construct($userid) {
        $db = new pdo_db();
        $this->db = $db;
        $this->userid = $userid;
    }

    function getContextId() {
        $query = "select contextid, userid  from mdl_role_assignments"
                . "   where userid=" . $this->userid . "";
        //echo "Query: " . $query . "<br/>";
        $num = $this->db->numrows($query);
        if ($num) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $contextid = $row['contextid'];
            }
            return $contextid;
        }
    }

    function getInstanceId() {
        $contextid = $this->getContextId();
        if ($contextid) {
            $query = "select * from mdl_context"
                    . "  where contextlevel=50 and"
                    . "  id=$contextid";
            // echo "Query: " . $query . "<br/>";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $instanceid = $row['instanceid'];
                }
                return $instanceid;
            }
        }
    }

    function getUserRole() {
        $query = "select * from mdl_role_assignments"
                . "   where userid=" . $this->userid . "";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $roleid = $row['roleid'];
            }
            return $roleid;
        }
    }

}
