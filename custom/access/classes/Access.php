<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Access extends Utils {

    function __construct() {
        parent::__construct();
    }

    function has_access($userid) {
        $now = time();
        $status = 0;

        // 1. Check among trial keys
        $query = "select * from mdl_trial_keys where userid=$userid";
        //echo "Query: ".$query."<br>";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $exp_date = $row['exp_date'];
                if ($exp_date >= $now) {
                    $status = 1;
                }
            }
        } // end if $num>0
        // 2. Check among paid keys
        $query = "select * from mdl_card_payments where userid=$userid";
        //echo "Query: ".$query."<br>";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $exp_date = $row['exp_date'];
                if ($exp_date >= $now) {
                    $status = 1;
                }
            }
        } // end if $num>0
        //echo "Function status: " . $status . "<br>";
        return $status;
    }

    function get_acces_dialog($userid, $groups) {
        $groups_list = implode(',', $groups);
        $list = "";
        $list.="<br><br>";
        $list.="<div class='container-fluid' style='text-align:center;'>";
        $list.="<span class='span12'><h1>Globalization Plus<br>Nonpartisan Current Events Reports for University Students & Faculty</h1></span>";
        $list.="</div>";

        $list.="<div class='container-fluid' style='text-align:center;'>";
        $list.="<span class='span12'>You do not have subscription or it is expired. Please click <a href='http://" . $_SERVER['SERVER_NAME'] . "/lms/payments/payment.php?userid=$userid&groups=$groups_list' target='_blank'>here</a> to get your subscription.</span>";
        $list.="</div>";
        return $list;
    }

    function has_confirmed() {
        
    }

}
