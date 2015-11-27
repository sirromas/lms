<?php

require_once '../config.php';
require_once("$CFG->dirroot/class.pdo.database.php");

class tutorUsers {

    private $db;

    function __construct() {
        $db = new pdo_db();
        $this->db = $db;
    }

    function getTutorGroups() {
        global $COURSE, $USER;
        //print_r($COURSE);
        //echo "<br/>...........................<br/>";
        $tutor_groups = array();
        $query = "SELECT * FROM mdl_groups_members gm
        JOIN mdl_groups g ON g.id = gm.groupid
        WHERE gm.userid =$USER->id AND
        g.courseid=$COURSE->id ORDER BY name ASC";
        //echo "Query: ".$query. "<br/>";
        $num = $this->db->numrows($query);
        if ($num>0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $tutor_groups[] = $row['name'];
            }
            return $tutor_groups;
        }
    }

}

//$tu=new tutorUsers();
//$tutor_groups=$tu->getTutorGroups();
//print_r($tutor_groups);
