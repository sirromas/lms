<?php

require_once '../payments/Classes/PlaceOrder.php';

class Tutors {

    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }

    function getGroupsList() {
        $list = "";
        $list = $list . "<select id='groups' name='groups' style='background-color: rgb(250, 255, 189);width: 178px;'>";
        $list = $list . "<option value='0' selected>Please select group</option>";
        $query = "select id, name from mdl_groups";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $list = $list . "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        $list = $list . "</select>";
        return $list;
    }

    function confirmTutor($email, $code, $groupid, $page) {
        $status = '';
        $codeStatus = $this->checkTutorCode($code, $groupid);
        $emailStatus = $this->checkEmailStatus($email);
        $pageStatus = $this->checkTutorPage($page, $email);
        if ($codeStatus > 0 && $emailStatus > 0 && $pageStatus > 0) {
            $this->updateTutorStatus($email);
            $status = $status . "Your membership is confirmed";
        } else {
            $status = $status . "Wrong tutor data";
        }
        return $status;
    }

    function updateTutorStatus($email) {
        $query = "update mdl_user set confirmed=1 where username='$email'";
        $this->db->query($query);
    }

    function checkTutorCode($code, $groupid) {
        $query = "select groupid, code "
                . "from mdl_group_codes "
                . "where groupid=$groupid and code='$code'";
        return $this->db->numrows($query);
    }

    function checkEmailStatus($email) {
        $query = "select username from mdl_user where username='$email'";
        return $this->db->numrows($query);
    }

    function checkTutorPage($page, $email) {
        $pagecontent = file_get_contents($page);
        if (strpos($pagecontent, $email) !== false) {
            return 1;
        } else {
            return 0;
        }
    }

}
