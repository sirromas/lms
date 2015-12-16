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
        return $this->db->numrows($query);
    }

    function verifyPromocode($code) {
        
    }

    function verifyUserType($username) {

        /*****************************************************
         *          1 manager
         *          2 coursecreator
         *          3 editingteacher 
         *          4 teacher
         *          5 student  
         *          6 guest 
         ****************************************************/

        $query = "select email, username "
                . "from mdl_user where email='$username' "
                . "or username='$username'";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $userid = $row['id'];
        }
        $query = "select roleid, userid from mdl_role_assignments "
                . "where  userid=$userid";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $roleid = $row['roleid'];
        }
        if ($roleid == $this->user_type) {
            return 1;
        } else {
            return 0;
        }
    }

    function verifyCode($code) {
        // Temporary workaround untill payment system will work
        return 1;
    }

    function verifyUser($username, $code) {
        $type = $this->verifyUserType($username);
        if ($type == 1) {
            if ($this->user_type == 5) {
                $code_status = $this->verifyCode($code);
            } // end if $this->user_type==5
            else {
                $code_status = 1;
            }
        } // end if $type
        return array('type' => $type, 'code' => $code_status);
    }

}
