<?php
require_once ('class.database.php');

/**
 *
 * @author sirromas
 *        
 */
class signup_user
{
    private $signup_form="";
    private $user_type;
    
    function __construct($use_type)
    {
        $this->user_type=$use_type;
        $db=DB::getInstance();
        echo "User provided: " .$this->user_type;
    }
    
    function getSignUpForm(){
        return  "This is sighup form for $this->user_type";
    }
    
    
}

?>