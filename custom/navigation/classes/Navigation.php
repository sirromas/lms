<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Navigation extends Utils {

    public $page_module_id = 15;
    public $assesment_module_id = 1;
    public $forum_module_id = 9;
    public $quiz_module_id = 16;
    public $glossary_module_id = 10;

    function __construct() {
        parent::__construct();
    }

    function get_section_data($moduleid) {
        $pageid = 0;
        $usergroups = $this->get_user_groups();
        $query = "select * from mdl_course_modules "
                . "where module=$moduleid "
                . "and visible=1 and availability is not null "
                . "order by added desc limit 0,1";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            //echo "Inside num ...<br>";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $aval = json_decode($row['availability']);
            }

            $section = $aval->c;
            foreach ($section as $c) {
                $sectiongroups[] = $c->id;
            }

            foreach ($usergroups as $g) {
                if (in_array($g, $sectiongroups)) {
                    $pageid = $id; // If section and user are in same group
                }
            } // end foreach
        } // end if $num > 0
        return $pageid;
    }

    function get_assesment_id() {
        $pageid = $this->get_section_data($this->assesment_module_id);
        return $pageid;
    }

    function get_page_id() {
        $pageid = $this->get_section_data($this->page_module_id);
        return $pageid;
    }

    function get_forum_id() {
        $pageid = $this->get_section_data($this->forum_module_id);
        return $pageid;
    }

    function get_quiz_id() {
        $pageid = $this->get_section_data($this->quiz_module_id);
        return $pageid;
    }

    function get_glossary_id() {
        $pageid = $this->get_section_data($this->glossary_module_id);
        return $pageid;
    }

    function get_overrided_quiz_id() {
        $pageid = 0;
        $moduleid = $this->quiz_module_id;
        $usergroups = $this->get_user_groups();
        $query = "select * from mdl_course_modules "
                . "where module=$moduleid "
                . "and visible=1 order by added desc limit 0,1";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $quizid = $row['instance'];
            } // end while

            $query = "select * from mdl_quiz_overrides where quiz=$quizid";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $sectiongroups[] = $row['groupid'];
            }

            foreach ($usergroups as $g) {
                if (in_array($g, $sectiongroups)) {
                    $pageid = $id; // If section and user are in same group
                }
            } // end foreach
        } // end if $num > 0 
        return $pageid;
    }

    function get_subscription_info() {
        $list = "";
        $userid = $this->user->id;

        $query1 = "select * from mdl_trial_keys where userid=$userid";
        $num1 = $this->db->numrows($query1);
        if ($num1 > 0) {
            $result = $this->db->query($query1);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $trial_start = $row['start_date'];
                $trial_end = $row['exp_date'];
            } // end while
        } // end if $num1 > 0

        $query2 = "select * from mdl_card_payments where userid=$userid";
        $num2 = $this->db->numrows($query2);
        if ($num2 > 0) {
            $result = $this->db->query($query2);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $paid_start = $row['start_date'];
                $paid_end = $row['exp_date'];
            } // end while
        } // end if $num2 > 0

        if ($num1 == 0 && $num2 > 0) {
            $s = date('m-d-Y', $paid_start);
            $e = date('m-d-Y', $paid_end);
        } // end if

        if ($num1 > 0 && $num2 == 0) {
            $s = date('m-d-Y', $trial_start);
            $e = date('m-d-Y', $trial_end);
        } // end if

        if ($num1 > 0 && $num2 > 0) {
            $s = date('m-d-Y', $paid_start);
            $e = date('m-d-Y', $paid_end);
        } // end if

        $list.="<span style=''>Subscription period: $s - $e</span>";

        return $list;
    }

}
