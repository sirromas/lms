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

class CustomSignup {

    private $db;
    private $user;
    public $user_type;
    public $userid;

    function __construct($user) {
        $this->user = $user;
        $this->db = DB::getInstance();
        $this->userid = $this->getUserId();
    }

    function getUserId() {
        $query = "select id from mdl_user where username='" . $this->user->email . "'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $userid = $row['id'];
        }
        return $userid;
    }

    function getCourseContext($courseid, $roleid) {
        $query = "select id from mdl_context
                     where contextlevel=50
                     and instanceid='" . $courseid . "' ";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $contextid = $row['id'];
        }
        return $contextid;
    }

    function getEnrolId($courseid) {
        $query = "select id from mdl_enrol
                     where courseid=" . $courseid . " and enrol='manual'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $enrolid = $row['id'];
        }
        return $enrolid;
    }

    function assignRoles($userid, $courseid, $role) {
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

    function setGroupName($courseid, $groupid) {
        $query = "update mdl_groups
                  set idnumber='Group_" . $courseid . "_" . $groupid . "',
                      name='Group_" . $courseid . "_" . $groupid . "'
                      where id=$groupid";
        $result = $this->db->query($query);
    }

    function getTutorGroups() {
        $query = "select groupid from mdl_tutor_groups
            where tutor_email='" . $this->user->email . "' "
                . "order by groupid desc limit 0,1";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $groups[] = $row['groupid'];
        }
        return $groups;
    }

    function createCourseGroups($courseid, $groups) {
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

    function createNewGroup($courseid, $group_name) {

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
                              'GP',
                              '" . $group_name . "',
                              '" . $this->user->email . "',
                              '1',    
                              '" . time() . "',
                              '" . time() . "')";
        // echo "<br/>Query: $query<br/>";
        $result = $this->db->query($query);
        $last_insert_id = mysql_insert_id();
        //$this->setGroupName($courseid, mysql_insert_id());
        // 2. Insert into mdl_tutor_groups
        $query = "insert into mdl_tutor_groups
                         (courseid, 
                          groupid,
                          tutor_email)
                          values ('" . $courseid . "',
                                  '" . $last_insert_id . "',
                                  '" . $this->user->email . "')";
        // echo "<br/>Query: $query<br/>";
        $result = $this->db->query($query);
    }

    function addUserToGroups($userid, $courseid, $groupid, $user_type) {

        /*
         * 
          echo "User id: ".$userid."<br/>";
          echo "User type: ".$this->user->type."<br/>";
          echo "Course id: ".$courseid."<br/>";
          echo "Group id:".$groupid."<br/>";
         * 
         */

        if ($this->user->type == 'tutor') {
            // echo "Inside get Tutors groups list ...<br/>";
            $groups = $this->getTutorGroups();
            foreach ($groups as $groupid) {
                groups_add_member($groupid, $userid);
            }
        } // end if $this->user->user_type == 'tutor' 
        else {
            // echo "<br/>GroupID inside else when user is not tutor: $groupid<br/>";
            $student_groupid=$this->getGroupIdByName($groupid);
            groups_add_member($student_groupid, $userid);
        }
    }

    function updateUserAddress() {
        $query = "update mdl_user set 
                  city='" . $this->user->address . "', 
                  country='USA'                
                  where id='" . $this->userid . "'";
        $result = $this->db->query($query);
    }

    function updateTutorData() {
        $query = "update mdl_user set                   
                  title='" . $this->user->title . "' ,
                  department='" . $this->user->department . "' ,       
                  school='" . $this->user->school . "'          
                  where id='" . $this->userid . "'";
        $result = $this->db->query($query);
    }

    function updateStudentData() {
        $query = "update mdl_user set                   
                  school='" . $this->user->school . "'      
                  where id='" . $this->userid . "'";
        $result = $this->db->query($query);
    }

    function getGroupIdByName($group_name) {
        $query="select id, name from mdl_groups "
                . "where name='$group_name'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $id=$row['id'];
        }
        return $id;        
    }

    function processCourseRequest() {
        $this->updateUserAddress();

        /*
          echo "User object: <pre>";
          print_r($this->user);
          echo "</pre>";
         */

        if ($this->user->type == 'tutor') {
            $this->updateTutorData();
            $this->createNewGroup($this->user->course, $this->user->group_name);
        }  // end if $this->user->type == 'tutor'        
        else {
            $this->updateStudentData();
        }
        $this->assignRoles($this->userid, $this->user->course, $this->user->type);
        $this->addUserToGroups($this->userid, $this->user->course, $this->user->group, $this->user->type);

        // We call this condition second time because 
        // tutor groups must be exits and tutor must be already added to it
        if ($this->user->type == 'tutor') {
            // Send confirmatio  email to tutor
            $supportuser = core_user::get_support_user();
            $subject = get_string('emailconfirmationsubject', '', format_string($site->fullname));
            $messagehtml = getTutorWelcomeMessage($this->user);
            $message = '';
            return email_to_user($this->user, $supportuser, $subject, $message, $messagehtml);
        }
    }

}

?>