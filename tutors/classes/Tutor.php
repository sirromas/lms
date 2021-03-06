<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Tutor extends Utils {

    function __construct() {
        parent::__construct();
    }

    function get_confirmation_form($userid) {
        $list = "";

        if (is_numeric($userid) && $userid > 0) {
            $user = $this->get_user_details($userid);

            $list.= "<div class='form-group' style='text-align:center;'>";
            $list.= "<label for='fio'>In order to confirm your membership please provide "
                    . "link to the web page which contains your Name, Surname, Title and Email  "
                    . "and confirms you are professor. </label>";
            $list.="</div>";

            $list.= "<div class='form-group'>";
            $list.= "<label for='fio'>Name and Surname</label>";
            $list.= "<input type='text' class='form-control' name='username' disabled id='username' value='$user->firstname $user->lastname'>";
            $list.="</div>";

            $list.= "<div class='form-group'>";
            $list.= "<label for='email'>Email</label>";
            $list.= "<input type='text' class='form-control' name='email' disabled id='email' value='$user->email'>";
            $list.="</div>";

            $list.= "<div class='form-group'>";
            $list.= "<label for='page'>Reference page URL:</label>";
            $list.= "<input type='text' class='form-control' required name='page' id='page' >";
            $list.="</div>";

            $list.="<div class='form-group'>";
            $list.="<div class='text-error' id='form_err'></div>";
            $list.="</div>";

            $list.="<div class='form-group'>";
            $list.="<div class='text-info' id='form_info'></div>";
            $list.="</div>";

            $list.="<div class='form-group'>";
            $list.="<div class='text-info' id='ajax_loader' style='display:none;text-align: center;'><img src='http://globalizationplus.com/assets/images/ajax.gif'></div>";
            $list.="</div>";


            $list.="<button type='submit' id='confirm' class='btn btn-default'>Submit</button>";
        } // end if is_numeric($userid) && $userid>0
        else {
            $list.= "<div class='container-fluid' style='text-align:center;'>";
            $list.="<span class='span12'>Invalid user data</span>";
            $list.="</div>";
        } // end else

        return $list;
    }

    function test_page($user, $output = TRUE) {
        $list = "";
        $page = file_get_contents($user->url);
        $status1 = strstr($page, $user->email);
        $status2 = strstr($page, $user->username);
        if ($status1 !== FALSE && $status2 !== FALSE) {
            $query = "update mdl_user set policyagreed='1' where email='$user->email'";
            $this->db->query($query);
            if ($output) {
                $list.="Thank you. Your membership is confirmed";
            } // end if
            else {
                return TRUE;
            }
        } // end if
        else {
            if ($output) {
                $list.="Your membership was not confirmed";
            } // end if 
            else {
                return FALSE;
            } // end else
        } // end else
        return $list;
    }

}
