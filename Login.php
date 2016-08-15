<?php

require_once ('class.database.php');

class Login {

    private $db;
    private $user_type;

    function __construct($user_type) {
        $this->user_type = $user_type;
        $db = DB::getInstance();
        $this->db = $db;
    }

    function verifyPassword($password, $fasthash = true) {
        require_once('lib/password_compat/lib/password.php');
        $options = ($fasthash) ? array('cost' => 4) : array();
        $hash_password = password_hash($password, PASSWORD_DEFAULT, $options);
        $query = "select password from mdl_user "
                . "where password='$hash_password'";
        // echo "Query: ".$query."<br/>";
        return $this->db->numrows($query);
    }

    function verifyUserType($username) {

        /*         * ***************************************************
         *          1 manager
         *          2 coursecreator
         *          3 editingteacher 
         *          4 teacher
         *          5 student  
         *          6 guest 
         * ************************************************** */

        $query = "select id, email, username "
                . "from mdl_user where email='$username' "
                . "or username='$username'";
        // echo "Query: ".$query."<br/>";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $userid = $row['id'];
        }
        $query = "select roleid, userid from mdl_role_assignments "
                . "where  userid=$userid";
        // echo "Query: ".$query."<br/>";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $roleid = $row['roleid'];
        }
        if ($roleid < 4) {
            return 1;
        } else {
            if ($roleid == $this->user_type) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    function verifyPromoCode($code) {
        $now = time();
        $query = "select code, active, expire_date "
                . "from mdl_promo_code "
                . "where active=1 and code='" . $code . "' "
                . "and expire_date>$now";
        //echo "Query: ".$query."<br/>";
        return $this->db->numrows($query);
    }

    function verifyPaidCode($email, $code) {
        $now = time();
        $query = "select email, enrol_key, exp_date "
                . "from mdl_enrol_key "
                . "where enrol_key='" . $code . "' "
                . "and email='" . $email . "' "
                . "and exp_date>$now";
        //echo "Query: ".$query."<br/>";
        return $this->db->numrows($query);
    }

    function verifyCode($email, $code) {
        $promo_status = $this->verifyPromoCode($code);
        $paid_status = $this->verifyPaidCode($email, $code);
        $status = ($promo_status > 0) ? $promo_status : $paid_status;
        return $status;
    }

    function verifyUser($username, $code) {
        /*
        $type = $this->verifyUserType($username);
        if ($type == 1) {
            // User type is match
            if ($this->user_type == 5) {
                $code_status = $this->verifyCode($username, $code);
            } // end if $this->user_type==5
            else {
                $code_status = 1;
            }
        } // end if $type
        else {
            $code_status = 0;
        }
        */
        //return array('type' => $type, 'code' => $code_status);
        return array('type' => 1, 'code' => 1);
    }

    function getUserEmailById($userid) {
        $query = "select id, email from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $email = $row['email'];
        }
        return $email;
    }

    function updateStudentEnrollKey($userid, $code) {
        $email = $this->getUserEmailById($userid);
        $promo_status = $this->verifyPromoCode($code);
        $paid_status = $this->verifyPaidCode($email, $code);
        if ($promo_status > 0 || $paid_status > 0) {
            $query = "update mdl_user set enroll_key='$code' "
                    . "where id=$userid";
            $this->db->query($query);
            $status = 'ok';
        } // end if $promo_status>0 || $paid_status>0
        else {
            $status = 'failed';
        }
        return $status;
    }

}
