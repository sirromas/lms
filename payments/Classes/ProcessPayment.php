<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php');

/**
 * Description of ProcessPayment
 *
 * @author sirromas
 */
class ProcessPayment {
    
    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }   
    
    
}
