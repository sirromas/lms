<?php

require_once '../payments/Classes/PlaceOrder.php';

class utils {

    private $db;
    private $order;

    function __construct() {
        $this->db = DB::getInstance();
        $this->order = new PlaceOrder();
    }

    function getGrpoupIds() {
        $group_id = array();
        $query = "select id from mdl_groups";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $group_id[] = $row['id'];
        }
        return $group_id;
    }

    function geGroupCodesIds() {
        $group_id_codes = array();
        $query = "select groupid from mdl_group_codes";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $group_id_codes[] = $row['groupid'];
        }
        return $group_id_codes;
    }

    function getCourseName($id) {
        $query = "select id, fullname from mdl_course"
                . " where id=" . $id . "";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $name = $row['fullname'];
        }
        return $name;
    }

    function getGroupData($id) {
        $query = "select id, courseid, idnumber, name "
                . "from mdl_groups where id=" . $id . "";
        $group = new stdClass();
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $group->groupid = $id;
            $group->courseid = $row['courseid'];
            $group->idnumber = $row['idnumber'];
            $group->name = $row['name'];
            $group->code = $this->order->generateRandomString();
        }
        return $group;
    }

    function createGrpoupCodes() {
        $groups = $this->getGrpoupIds();
        $group_codes = $this->geGroupCodesIds();
        $missed_groups = array_diff($groups, $group_codes);

        if (count($missed_groups) > 0) {
            foreach ($missed_groups as $groupid) {
                $groupObj = $this->getGroupData($groupid);
                $query = "insert into mdl_group_codes "
                        . "(groupid, courseid, idnumber, name, code)"
                        . " values('" . $groupObj->groupid . "',  
                                   '" . $groupObj->courseid . "',  
                                   '" . $groupObj->idnumber . "', 
                                   '" . $groupObj->name . "', 
                                   '" . $groupObj->code . "')";
                $result = $this->db->query($query);
            }
        }
    }

    function getGroupPage() {
        $this->createGrpoupCodes();
        $list = ""; 
        $list = $list . "<div class='wrapper clearfix'>
           <div align='center'>
              <section class='userLogin userForm clearfix oneCol'>
                   <div class='loginForm dsR21'>
                     <div class='CSSTableGenerator' id='signupwrap'
                     style='table-layout: fixed; width: 620px; align: center;'>                                
                      <table>";
        $list = $list . "<tr>";
        $list = $list . "<th>ID</th><th>Course Name</th>"
                . "<th>Group Name</th><th>Group Code</th>";
        $list = $list. "</tr>";        

        $query = "select * from mdl_group_codes";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $list = $list . "<tr>";
            $list = $list . "<td style='font-size: 12px;'>" . $row['groupid'] . "</td>"
                    . "<td style='font-size: 14px;'>" . $this->getCourseName($row['courseid']) . "</td>"
                    . "<td style='font-size: 14px;'>" . $row['name'] . "</td>"
                    . "<td style='font-size: 14px;'>" . $row['code'] . "</td>";
            $list = $list . "</tr>";
        }
        $list = $list . "</table></div></div></div></div >";        
        return $list;
    }

    function getPromoPage() {
        $list = "";                 
        $list = $list . "<div class='wrapper clearfix'>
           <div align='center' id='promo'>
              <section class='userLogin userForm clearfix oneCol'>
                   <div class='loginForm dsR21'>
                     <div class='CSSTableGenerator' id='signupwrap'
                     style='table-layout: fixed; width: 620px; align: center;'>                                
                      <table>";
        $list = $list . "<tr>";
        $list = $list . "<th>Code</th>"
                . "<th>Status</th><th>Expire Date</th>";
        $list = $list. "</tr>";     
        
        $query = "select * from mdl_promo_code order by expire_date desc limit 0,10";        
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $status=($row['active']==1) ? "Yes" : "No";
            $list = $list . "<tr>";
            $list = $list . "<td style='font-size: 12px;'>" . $row['code'] . "</td>"
                    . "<td style='font-size: 14px;'>" . $status . "</td>"
                    . "<td style='font-size: 14px;'>" . date('m/d/Y H:i:s', $row['expire_date']) . "</td>";                    
            $list = $list . "</tr>";
        }
        $list = $list . "</table></div></div></div></div>";        
        return $list;
    }

}
