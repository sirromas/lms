<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php');

class ProcessPayment {
    
    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }   
    
    
}
