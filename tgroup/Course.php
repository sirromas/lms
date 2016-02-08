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

    function generateRandomString($length = 25) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    function create_group_code($groupid, $group_name) {
        $courseid = 3;
        $code = $this->generateRandomString();
        $query = "insert into mdl_group_codes (groupid,"
                . "courseid,"
                . "name,"
                . "code) "
                . "values ($groupid, "
                . "$courseid , "
                . "'$group_name', "
                . "'$code')";
        $this->db->query($query);
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

        // 3. Create group secret code 
        $this->create_group_code($groupid, $group_name);
        return 0;
    }

    function create_new_groups($email, $code, $page, $groups) {

        $email_status = $this->checkEmailStatus($email);
        $code_status = 1; // Call was made by authorized user, so no need to check        
        $page_status = $this->checkTutorPage($page, $email);

        if ($email_status == 1 && $code_status == 1 && $page_status == 1) {
            for ($i = 0; $i < count($groups); $i++) {
                $status = $this->check_group($groups[$i]);
                if ($status == 0) {
                    $response = $this->create_tutor_group($groups[$i], $email);
                } // end if $status==0                
            } // end for
            $response = "<span align='center'>New courses has been created</span>";
        } // end if $email_status == 1 && $code_status == 1 && $page_status == 1
        else {
            $response = "<span align='center'>Wrong user  data</span>";
        } // end else
        return $response;
    }

    function getGroupNameById($id) {
        $query = "select id, name from mdl_groups where id=$id";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $name = $row['name'];
        }
        return $name;
    }

    function removeGroupMembers($groupid) {
        $query = "delete from mdl_groups_members "
                . "where groupid=$groupid";
        $this->db->query($query);
    }

    function deleteGroup($groupid) {
        $query = "delete from mdl_groups where id=$groupid";
        $this->db->query($query);
    }

    function deleteGroupCodes($groupid) {
        $query = "delete from mdl_group_codes where groupid=$groupid";
        $this->db->query($query);
    }

    function getTutorGroups($userid) {
        $list = '';
        $groups = array();
        $query = "select groupid, userid from mdl_groups_members "
                . "where userid=$userid";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $groups[] = $row['groupid'];
        }
        
        foreach ($groups as $groupid) {
            $group_name = $this->getGroupNameById($groupid);            
            $list.="<input type='checkbox' id='$groupid' class='tcourse' value='$groupid' ><span style='font: 13.3333px Arial;'>$group_name</span><br/>";            
        } // end foreach                
        return $list;
    }

    function deleteTutorGroups($email, $code, $page, $groups) {       
        $email_status = $this->checkEmailStatus($email);
        $code_status = 1; // Call was made by authorized user, so no need to check        
        $page_status = $this->checkTutorPage($page, $email);
        if ($email_status == 1 && $code_status == 1 && $page_status == 1) {
            foreach ($groups as $groupid) {
                $this->removeGroupMembers($groupid);
                $this->deleteGroupCodes($groupid);
                $this->deleteGroup($groupid);
            }
            $response = "<span align='center'>Selected courses were deleted</span>";
        } // end if $email_status == 1 && $code_status == 1 && $page_status == 1
        else {
            $response = "<span align='center'>Wrong user  data</span>";
        }
        return $response;
    }

}
