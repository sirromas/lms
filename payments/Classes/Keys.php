<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php');

class Keys {

    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }   

    function getKey($email, $key) {
        $date = time();
        $query = "select email, enrol_key, exp_date "
                . "from mdl_enrol_key "
                . "where email='$email' and enrol_key='$key'";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result=$this->db->query($query);
            while ($row = mysql_fetch_assoc($result)) {
                $exp_date = $row['exp_date'];
            }
            if ($exp_date >= $date) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
