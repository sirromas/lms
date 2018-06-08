<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Access extends Utils
{

    /**
     * Access constructor.
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $userid
     * @return int
     */
    function has_access($userid)
    {
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

    /**
     * @param $userid
     * @param $groups
     * @return string
     */
    function get_acces_dialog($userid, $groups)
    {
        $groups_list = implode(',', $groups);
        $list = "";
        $list .= "<br><br>";
        $list .= "<div class='container-fluid' style='text-align:center;'>";
        $list .= "<span class='span12'><h1>NewsFacts & Analysis<br>Nonpartisan Current Events Reports for University Students & Faculty</h1></span>";
        $list .= "</div>";

        $list .= "<div class='container-fluid' style='text-align:center;'>";
        $list .= "<span class='span12'><h3>You do not have subscription or it is expired. Please click <a href='http://www." . $_SERVER['SERVER_NAME'] . "/lms/payments/payment.php?userid=$userid&groups=$groups_list' target='_blank'>here</a> to get your subscription.</h3></span>";
        $list .= "</div>";
        return $list;
    }

    /**
     * @param $userid
     * @return mixed
     */
    function has_confirmed($userid)
    {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $confirmed = $row['policyagreed'];
        }
        return $confirmed;
    }

    /**
     * @param $userid
     * @param $groups
     * @return string
     */
    function get_tutor_access_dialog($userid, $groups)
    {
        $groups_list = implode(',', $groups);
        $list = "";
        $list .= "<br><br>";
        $list .= "<div class='container-fluid' style='text-align:center;'>";
        $list .= "<span class='span12'><h1>NewsFacts & Analysis<br>Nonpartisan Current Events Reports for University Students & Faculty</h1></span>";
        $list .= "</div>";

        $list .= "<div class='container-fluid' style='text-align:center;'>";
        $list .= "<span class='span12'>You did not confirm your professor's membership. Please click <a href='http://www." . $_SERVER['SERVER_NAME'] . "/lms/tutors/index.php?userid=$userid&groups=$groups_list' target='_blank'>here</a> to get confirmed.</span>";
        $list .= "</div>";
        return $list;
    }

    /**
     * @param $username
     * @return mixed
     */
    function get_user_id_by_username($username)
    {
        $query = "select * from mdl_user where username='$username'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }

    /**
     * @param $u
     * @param $p
     * @return int
     */
    function get_userid_by_credentials($u, $p)
    {
        $id = 0;
        $query = "select * from mdl_user where username='$u' and purepwd='$p'";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            }
        }
        return $id;
    }

}
