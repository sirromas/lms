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
            $list.= "<input type='text' class='form-control' name='page' id='page' >";
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

    function test_page($email, $url) {
        $list = "";
        $page = file_get_contents($url);
        $status = strpos($page, $email);
        if ($status !== FALSE) {
            $query = "update mdl_user set policyagreed='1' where email='$email'";
            $this->db->query($query);
            $list.="Thank you. Your membership is confirmed";
        } // end if
        else {
            $list.="Your membershop was not confirmed";
        } // end else
        return $list;
    }

}
