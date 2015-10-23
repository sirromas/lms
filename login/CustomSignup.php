<?php

/** 
 * @author sirromas
 * 
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require ('../config.php');
require ('../class.database.php');
require_once ('../group/lib.php');

class CustomSignup
{

    private $db;
    private $user;
    public $user_type;
    public $userid;

    function __construct($user)
    {
        $this->user = $user;
        $this->db = DB::getInstance();
        $this->userid = $this->getUserId();
    }

    function getUserId()
    {
        $query = "select id from mdl_user where username='" . $this->user->email . "'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $userid = $row['id'];
        }
        return $userid;
    }

    function getCourseContext($courseid, $roleid)
    {
        $query = "select id from mdl_context
                     where contextlevel=50
                     and instanceid='" . $courseid . "' ";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $contextid = $row['id'];
        }
        return $contextid;
    }

    function getEnrolId($courseid)
    {
        $query = "select id from mdl_enrol
                     where courseid=" . $courseid . " and enrol='manual'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $enrolid = $row['id'];
        }
        return $enrolid;
    }

    function assignRoles($userid, $courseid, $role)
    {
        $roleid = ($role == 'student') ? 5 : 4;
        $enrolid = $this->getEnrolId($courseid);
        $contextid = $this->getCourseContext($courseid, $roleid);
        
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
        $result = $this->db->query($query);
        
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
        $result = $this->db->query($query);
    }

    function setGroupName($courseid, $groupid)
    {
        $query = "update mdl_groups
                  set idnumber='Group_" . $courseid . "_" . $groupid . "',
                      name='Group_" . $courseid . "_" . $groupid . "'
                      where id=$groupid";
        $result = $this->db->query($query);
    }

    function getTutorGroups()
    {
        $query = "select groupid from mdl_tutor_groups
            where tutor_email='" . $this->user->email . "'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $groups[] = $row['groupid'];
        }
        return $groups;
    }

    function createCourseGroups($courseid, $groups)
    {
        for ($i = 1; $i <= $groups; $i ++) {
            
            // 1. Insert into mdl_groups
            $query = "insert into mdl_groups
                     (courseid,
                      idnumber,
                      name,  
                      description,
                      descriptionformat,  
                      timecreated,
                      timemodified)
                      values ('" . $courseid . "',
                              '" . some_temp_id . "',
                              '" . some_temp_name . "',
                              '" . $this->user->email . "',
                              '1',    
                              '" . time() . "',
                              '" . time() . "')";
            $result = $this->db->query($query);
            $last_insert_id = mysql_insert_id();
            $this->setGroupName($courseid, mysql_insert_id());
            
            // 2. Insert into mdl_tutor_groups
            $query = "insert into mdl_tutor_groups
                         (courseid, 
                          groupid,
                          tutor_email)
                          values ('" . $courseid . "',
                                  '" . $last_insert_id . "',
                                  '" . $this->user->email . "')";
            $result = $this->db->query($query);
        }
    }

    function addUserToGroups($userid, $courseid, $groupid, $user_type)
    {
        if ($user_type == 'tutor') {
            $groups = $this->getTutorGroups();
            foreach ($groups as $groupid) {
                groups_add_member($groupid, $userid);
            }
        } else {
            groups_add_member($groupid, $userid);
        }
    }

    function processCourseRequest()
    {
        if ($this->user->type == 'tutor') {
            $this->assignRoles($this->userid, $this->user->course, $this->user->type);
            $this->createCourseGroups($this->user->course, $this->user->group);
            $this->addUserToGroups($this->userid, $this->user->course, 0, $this->user->type);
        } else {
            $this->assignRoles($this->userid, $this->user->course, $this->user->type);
            $this->addUserToGroups($this->userid, $this->user->course, $this->user->group, $this->user->type);
        }
    }
}

?>