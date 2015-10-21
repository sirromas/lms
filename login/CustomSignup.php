<?php

/** 
 * @author sirromas
 * 
 */

require ('../config.php');
require_once '../class.database.php';

class CustomSignup
{
    
    private $db;
    private $user;
    public $userid;
    
    function __construct($user)  {
        $this->user=$user;
        $db = DB::getInstance();
        $this->db = $db;
        $this->userid=$this->getUserId();        
    }
    
    function getUserId () {
        $query="select id from ";
    }
    
    function assignRoles ($userid, $courseid, $role) {
    
    }
    
    
        
    function createCourseGroups ($courseId, $groupsNum) {
        
    }    
    
    
    function addUserToGroups ($userId, $goupsList) {
        
    }
        
    function processCourseRequest () {
        
        if ($this->user->usertype=='tutor') {
            
        }
        
    }
    
    
    
}

?>