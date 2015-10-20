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
        $query="select email from mdl_user where email='$email'";
        //echo $query ."<br/>";            
        return $this->db->numrows($query);
    }
    
    function verifyPassword ($password, $fasthash=true) {        
        require_once('lib/password_compat/lib/password.php');
        $options = ($fasthash) ? array('cost' => 4) : array();
        $hash_password = password_hash($password, PASSWORD_DEFAULT, $options);     
        $query="select password from mdl_user where password='$hash_password'";        
        return $this->db->numrows($query);
    }

    function verifyCode ($code) {
        // Temporary workaround untill payment system will work
        return 1;
    }
    
    
}

?>