<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/authorize/Payment.php';

class StudentPayment extends Utils {

    function __construct() {
        parent::__construct();
    }

    function get_class_dropdown($groups) {
        $list = "";
        if (count($groups) > 1) {
            $list.= "<div class='form-group'>";
            $list.="<select id='class' name='class' class='form-control'>";
            $list.="<option value='0' selected>Class</option>";
            foreach ($groups as $groupid) {
                $groupname = $this->get_group_name($groupid);
                $list.="<option value='$groupid'>$groupname</option>";
            }
            $list.="</select>";
            $list.="</div>";
        } // end if
        else {
            $groupname = $this->get_group_name($groups[0]);
            $list.= "<div class='form-group'>";
            $list.="<select id='class' name='class' class='form-control'>";
            $list.="<option value='$groups[0]' selected>$groupname</option>";
            $list.="</select>";
            $list.="</div>";
        } // end else

        return $list;
    }

    function get_month_dropdown() {
        $list = "";
        $list.= "<div class='form-group'>";
        $list.="<select id='cardmonth' name='cardmonth' required class='form-control'>";
        $list.="<option value='0' selected>Month</option>";
        for ($i = 1; $i <= 12; $i++) {
            $list.="<option value='$i'>$i</option>";
        }
        $list.="</select>";
        return $list;
    }

    function get_year_dropdown() {
        $list = "";
        $list.= "<div class='form-group'>";
        $list.="<select id='cardyear' name='cardyear' required class='form-control'>";
        $list.="<option value='0' selected>Year</option>";
        for ($i = 2016; $i <= 2036; $i++) {
            $list.="<option value='$i'>$i</option>";
        }
        $list.="</select>";
        return $list;
    }

    function get_states_dropdown() {
        $list = "";
        $list.= "<div class='form-group'>";
        $list.="<select id='state' name='state' class='form-control'>";
        $list.="<option value='0' selected>State*</option>";
        $query = "select * from mdl_states";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $list.="<option value='" . trim($row['id']) . "'>" . $row['state'] . "</option>";
        }
        $list.="</select>";
        return $list;
    }

    function get_student_payment_form($userid, $groupslist) {
        $user = $this->get_user_details($userid);
        $list = '';
        $groups = explode(',', $groupslist);
        $state = $this->get_states_dropdown();
        $m = $this->get_month_dropdown();
        $y = $this->get_year_dropdown();
        $class = $this->get_class_dropdown($groups);
        if ($userid > 0 && $groups[0] > 0) {
            $list.="<form id='student_payment' method='post'>";
            $list.="<input type='hidden' id='amount' name='amount' value='27'>";
            $list.="<div class='form-group'>
                            <label for='holder'>Card Holder Name*</label>
                            <input type='text' class='form-control' required disabled='true' id='holder' data-userid=$userid  name='holder' required placeholder='Enter Card Holder Name' value='$user->firstname $user->lastname'>
                        </div>";
            $list.="<div class='form-group'>
                            <label for='cardnumber'>Card Number*</label>
                            <input type='text' class='form-control' id='cardnumber' name='cardnumber' required placeholder='Enter Card Number'>
                        </div>";
            $list.="<div class='form-group'>
                            <label for='cvv'>CVV Code*</label>
                            <input type='text' class='form-control' id='cvv' name='cvv' required placeholder='Enter Card Code*'>
                        </div>";
            $list.="<div class='form-group'>
                            <label for='month'>Expiration month*</label>
                            $m
                        </div>";
            $list.="<div class='form-group'>
                            <label for='year'>Expiration year*</label>
                            $y
                        </div>";
            $list.="<div class='form-group'>
                            <label for='class'>Class*</label>
                            $class
                        </div>";
            $list.="<div class='form-group'>
                            <label for='address'>Address*</label>
                            <input type='text' class='form-control' id='address' name='address' required placeholder='Enter Address*'>
                        </div>";
            $list.="<div class='form-group'>
                            <label for='state'>State*</label>
                            $state
                        </div>";
            $list.="<div class='form-group'>
                            <label for='city'>City*</label>
                            <input type='text' class='form-control' id='city' name='city' required placeholder='City*'>
                        </div>";
            $list.="<div class='form-group'>
                            <label for='zip'>ZIP*</label>
                            <input type='text' class='form-control' id='zip' name='zip' required placeholder='Enter ZIP*'>
                        </div>";
            $list.="<div class='form-group'>
                            <div class='text-error' id='form_err'></div>    
                        </div>";
            $list.="<div class='form-group'>
                            <div class='text-info' id='form_info'></div>    
                        </div>";

            $list.="<div class='form-group'>
                            <div class='text-info' id='ajax_loader' style='display:none;text-align: center;'><img src='http://www.newsfactsandanalysis.com/assets/images/ajax.gif'></div>    
                        </div>";

            $list.="<button type='submit' class='btn btn-default'>Submit</button>";

            $list.="</form>";
        } // end if $userid>0 && count($groups)>0
        else {
            $list.="<div class = 'container-fluid' style = 'text-align:center;'>";
            $list.="<span class = 'span12'>Invalid user data</span>";
            $list.="</div>";
        }

        return $list;
    }

}
