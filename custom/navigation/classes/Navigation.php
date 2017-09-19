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
        // Course activity/resources are same for all student's groups
        $pageid = 0;
        $query = "select * from mdl_course_modules "
                . "where module=$moduleid "
                . "and visible=1 "
                . "order by id desc limit 0,1";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $pageid = $row['id'];
            } // end while
        } // end if $num > 0
        return $pageid;
    }

    function get_user_email($userid) {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $email = $row['email'];
        }
        return $email;
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
        $roleid = $this->get_user_role();
        if ($roleid != 0 && $roleid != 4 && $roleid != 3) {
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
        } // end if $roleid != 0

        return $list;
    }

    function get_previous_quiz_id() {
        $id = 0;
        $query = "select * from mdl_course_modules "
                . "where module=$this->quiz_module_id "
                . "order by id desc limit 1,2";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            } // end while
        } // end if $num > 0
        return $id;
    }

    function update_quiz_link() {
        $server = $_SERVER['SERVER_NAME'];
        $this->update_page_link();
        $old_id = $this->get_previous_quiz_id();
        if ($old_id == 0) {
            return;
        } // end if $oldid==0
        else {
            $old_link = "http://www.$server/lms/mod/quiz/view.php?id=$old_id";
            $new_id = $this->get_section_data($this->quiz_module_id);
            $new_link = "http://www.$server/lms/mod/quiz/view.php?id=$new_id";

            $forum_id = $this->get_section_data($this->forum_module_id);
            $query = "select * from mdl_course_modules "
                    . "where id=$forum_id";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $instanceid = $row['instance'];
            }

            $query = "select * from mdl_forum where id=$instanceid";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $intro = $row['intro'];
            }

            $new_intro = str_replace($old_link, $new_link, $intro);
            $query = "update mdl_forum set intro='$new_intro' where id=$instanceid";
            $this->db->query($query);
        } // end else
    }

    function get_previous_page_id() {
        $id = 0;
        $query = "select * from mdl_course_modules "
                . "where module=$this->page_module_id "
                . "order by id desc limit 1,2";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            } // end while
        } // end if $num > 0
        return $id;
    }

    function update_page_link() {
        $old_id = $this->get_previous_page_id();
        if ($old_id == 0) {
            return;
        } // end if $oldid==0
        else {
            $old_link = "http://www" . $_SERVER['SERVER_NAME'] . "/lms/mod/page/view.php?id=$old_id";
            $new_id = $this->get_section_data($this->page_module_id);
            $new_link = "http://www" . $_SERVER['SERVER_NAME'] . "/lms/mod/page/view.php?id=$new_id";

            $quiz_id = $this->get_section_data($this->quiz_module_id);
            $query = "select * from mdl_course_modules "
                    . "where id=$quiz_id";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $instanceid = $row['instance'];
            }

            $query = "select * from mdl_quiz where id=$instanceid";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $intro = $row['intro'];
            }

            $new_intro = str_replace($old_link, $new_link, $intro);
            $query = "update mdl_quiz set intro='$new_intro' where id=$instanceid";
            $this->db->query($query);
        } // end else
    }

    function is_logged() {
        if (isloggedin()) {
            return 1;
        } // end if
        else {
            return 0;
        } // end else
    }

    function get_section_instance($id) {
        $query = "select * from mdl_course_modules where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $instanceid = $row['instance'];
        }
        return $instanceid;
    }

    function filter_content($data) {
        $needle = '<div id="container20"></div>';
        $pos = strpos($data, $needle);
        $content = substr($data, ($pos - 20));
        return $content;
    }

    function get_arcticle_content($url) {
        $list = "";
        $replace = 'http://www.newsfactsandanalysis.com/lms/mod/page/view.php?id=';
        $id = trim(str_replace($replace, '', $url));
        $instanceid = $this->get_section_instance($id);
        $query = "select * from mdl_page where id=$instanceid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $content = $row['content'];
        }
        $list.=$content;
        return $list;
    }

    function get_dictionary_content() {
        $http_path = 'http://www.newsfactsandanalysis.com/dictionary/dictionary.html';
        $list = file_get_contents($http_path);
        return $list;
    }

    function get_archive_page($url) {
        $list = file_get_contents($url);
        return $list;
    }

    function get_course_grade_items($courseid) {
        $query = "select * from mdl_grade_items "
                . "where courseid=$courseid "
                . "and itemmodule is not null  ";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $items[] = $row['id'];
            } // end while
        } // end if $num > 0
        return $items;
    }

    function get_item_grade($item, $userid) {
        $query = "select * from mdl_grade_grades "
                . "where itemid=$item "
                . "and userid=$userid "
                . "and finalgrade is not null ";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $pr = new stdClass();
                $name = $this->get_grade_item_name($item);
                $date = date('m-d-Y', $row['timemodified']);
                if ($row['finalgrade'] < 100) {
                    $grade = round($row['finalgrade']);
                } // end if 
                else {
                    $grade = round(($row['finalgrade'] / $row['rawgrademax']) * 100);
                } // end else 
                $pr->id = $item;
                $pr->name = $name;
                $pr->grade = $grade;
                $pr->date = $date;
                $pr->max = round($row['rawgrademax']);
            } // end while
        } // end if $num > 0
        else {
            $pr = null;
        }
        return $pr;
    }

    function get_grade_item_name($id) {
        $query = "select * from mdl_grade_items where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['itemname'];
        }
        return $name;
    }

    function get_student_grades() {
        $list = "";
        $userid = $this->user->id;

        $items = $this->get_course_grade_items($this->courseid);
        if (count($items) > 0) {

            $list.="<br/><br/><div class='row-fluid' style='text-align:center;font-weight:bold;'>";
            $list.="<span class='span6' style='margin-left:10%'>My Grades</span>";
            $list.="</div><br/>";

            $list.="<div class='row-fluid' style='text-align:left;'>";
            $list.="<span class='span3'>Item name</span>";
            $list.="<span class='span2'>Item grade</span>";
            $list.="<span class='span2'>Item max point</span>";
            $list.="<span class='span2'>Finish date</span>";
            $list.="</div>";

            foreach ($items as $itemid) {
                $grade = $this->get_item_grade($itemid, $userid); // object

                $list.="<div class='row-fluid' style='text-align:left;'>";
                $list.="<span class='span3'>$grade->name</span>";
                $list.="<span class='span2'>$grade->grade</span>";
                $list.="<span class='span2'>$grade->max</span>";
                $list.="<span class='span2'>$grade->date</span>";
                $list.="</div>";

                $list.="<div class='row-fluid'>";
                $list.="<span class='span9'><hr/></span>";
                $list.="</div>";
            } // end foreach
        } // end if
        else {
            $list.="<div class='row-fluid' style='text-align:center;'>";
            $list.="<span class='span9'>You do not have any grades</span>";
            $list.="</div>";
        }
        return $list;
    }

}
