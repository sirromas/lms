<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.pdo.database.php';

class Tutor {

    public $db;
    public $user;
    public $course;
    public $session;

    function __construct() {
        global $USER, $COURSE, $SESSION;
        $db = new pdo_db();
        $this->db = $db;
        $this->user = $USER;
        $this->course = $COURSE;
        $this->session = $SESSION;
    }

    function is_group_exists($groupname) {
        $query="select * from mdl_groups where name='$groupname'";
        
    }

}
