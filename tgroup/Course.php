<?php

require_once '../tutors/tutors.php';

/**
 * Description of Course
 *
 * @author sirromas
 */
class Course extends Tutors {

    function check_group($group_name) {
        $query = "select name from mdl_groups where name='$group_name'";
        //echo "Query: " . $query . "<br/>";
        $num = $this->db->numrows($query);
        //echo "Group num: " . $num . "<br/>";
        return $num;
    }

    function get_user_id($email) {
        $query = "select id, username from mdl_user "
                . "where username='$email'";
        //echo "Query: " . $query . "<br/>";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $id = $row['id'];
        }
        return $id;
    }

    function create_tutor_group($group_name, $email) {
        $userid = $this->get_user_id($email);
        $courseid = 3; // we have only one course
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
                              '" . $email . "',
                              '" . $group_name . "',
                              '" . $email . "',
                              '1',    
                              '" . time() . "',
                              '" . time() . "')";
        //echo "Query: " . $query . "<br/>";
        $this->db->query($query);
        $groupid = mysql_insert_id();

        // 2. Insert into mdl_groups_members"
        $query = "insert into mdl_groups_members  (groupid,userid,timeadded)"
                . " values ('" . $groupid . "' , '" . $userid . "' ,'" . time() . "')";
        //echo "Query: " . $query . "<br/>";
        $this->db->query($query);
        return 0;
    }

    function create_new_groups($email, $code, $page, $groups) {
        $responses = array();
        $email_status = $this->checkEmailStatus($email);
        //echo "Email status: " . $email_status . "<br/>";
        $code_status = 1; // Call was made by authorized user, so need to check
        //echo "Code status: " . $code_status . "<br/>";
        $page_status = $this->checkTutorPage($page, $email);
        // echo "Page status: " . $page_status . "<br/>";
        // print_r($groups);
        //echo "<br/>";
        //echo "Groups number: " . count($groups) . "<br/>";

        if ($email_status == 1 && $code_status == 1 && $page_status == 1) {
            for ($i = 0; $i < count($groups); $i++) {
                //echo "Group name: " . $groups[$i] . "<br/>";
                $status = $this->check_group($groups[$i]);
                //echo "Group status ($groups[$i]): " . $status . "<br/>";
                if ($status == 0) {
                    //echo "Inside if  status == 0....<br/>";
                    $response = $this->create_tutor_group($groups[$i], $email);
                    $responses[] = $response;
                } // end if $status==0
                else {
                    //echo "Inside else ....<br/>";
                    $responses[] = 1;
                }
            } // end for
        } // end if $email_status == 1 && $code_status == 1 && $page_status == 1
        else {
            $responses = array('--', '--', '--', '--');
        } // end else
        return $responses;
    }

}
