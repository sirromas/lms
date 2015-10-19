<?php

require_once ('class.database.php');

class Login {
    
    private $db;
    
    private $user_type;
    
    function __construct($user_type)
    {
        $this->user_type = $user_type;
        $db = DB::getInstance();
        $this->db = $db;
    }
    
    function verifyEmail ($email) {
        
    }
    
    function verifyPassword ($password) {
        
    }

    function verifyCode ($code) {
        
    }
    
    
}

?>