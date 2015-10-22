<?php

/** 
 * @author sirromas
 * 
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require ('../class.database.php');
class CustomSignup
{

    private $db;

    private $user;

    public $userid;

    function __construct($user)
    {
        // print_r($user);
        // echo "<br/>-------------------------<br/>";
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
        $contextid=$this->getCourseContext($courseid, $roleid);       
        
        // 1.  Insert into mdl_user_enrolments table
        $query = "insert into mdl_user_enrolments
             (enrolid,
              userid,
              timestart,
              modifierid,
              timecreated,
              timemodified)
               values ('" . $enrolid . "',
                       '" . $userid . "',
                        '".time()."',   
                        '2',
                         '" . time() . "',
                         '" . time() . "')";        
        $result = $this->db->query($query);
        
        // 2. Insert into mdl_role_assignments table
        $query="insert into mdl_role_assignments
                  (roleid,
                   contextid,
                   userid,
                   timemodified,
                   modifierid)                   
                   values ('".$roleid."',
                           '".$contextid."',
                           '".$userid."',
                           '".time()."',
                            '2'         )";       
       $result = $this->db->query($query);        
    }

    function setGroupName($courseid, $groupid)
    {
        $query = "update mdl_groups
                  set idnumber='Group_'" . $courseid . "_" . $groupid . ",
                      name='Group_'" . $courseid . "_" . $groupid . "
                      where id=$groupid";
        $result = $this->db->query($query);
    }

    function getTutorGroups()
    {
        $query = "select id from mdl_groups 
            where description='" . $this->user->email . "'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $groups[] = $row['id'];
        }
        return $groups;
    }

    function createCourseGroups($courseid, $groups)
    {
        foreach ($groups as $item) {
            $query = "insert into mdl_groups
                     courseid='" . $courseid . "',
                     idnumber='some_temp_id',
                     name='some_temp_name',
                     description='" . $this->user->email . "',    
                     timecreated='" . time() . "',
                     timemodified='" . time() . "'";
            $result = $this->db->query($query);
            $this->setGroupName($courseid, mysql_insert_id());
        }
    }

    function addUserToGroups($userid, $courseid, $groupid, $user_type)
    {
        if ($user_type == 'tutor') {
            $groups = $this->getTutorGroups();
            foreach ($groups as $groupid) {
                $query = "insert into mdl_groups_members
                          groupid='" . $groupid . "',
                          userid='" . $this->userid . "',
                          timeadded='" . time() . "',
                          component=''";
                $result = $this->db->query($query);
            }
        } else {
            $query = "insert into mdl_groups_members
                          groupid='" . $groupid . "',
                          userid='" . $this->userid . "',
                          timeadded='" . time() . "',
                          component=''";
            $result = $this->db->query($query);
        }
    }

    function processCourseRequest()
    {
        if ($this->user->user_type == 'tutor') {
            $this->assignRoles($this->userid, $this->user->course, $this->user->user_type);
            // $this->createCourseGroups($this->user->course, $this->user->group);
            // $this->addUserToGroups($this->userid, $this->user->course, 0, $this->user->user_type);
        } else {
            $this->assignRoles($this->userid, $this->user->course, $this->user->user_type);
            // $this->addUserToGroups($this->userid, $this->user->course, $this->user->group, $this->user->user_type);
        }
    }
}

?>