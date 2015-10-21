<?php

/** 
 * @author sirromas
 * 
 */

require_once '../class.database.php';

class CustomSignup
{
    
    private $db;
    private $user;
    
    function __construct($user)  {
        $this->user=$user;
        $db = DB::getInstance();
        $this->db = $db;        
    }
    
    
    
}

?>