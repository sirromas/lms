<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';

/**
 * Description of Archive
 *
 * @author moyo
 */
class Archive {

    public $db;
    public $student_role = 5;
    public $tutor_role = 4;
    public $page_module_id = 15;
    public $assesment_module_id = 1;
    public $forum_module_id = 9;
    public $quiz_module_id = 16;
    public $glossary_module_id = 10;

    function __construct() {
        $this->db = new pdo_db();
    }

    function get_user_groups($userid) {
        $groups = array();
        $query = "select * from mdl_groups_members where userid=$userid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $groups[] = $row['groupid'];
            } // end while
        } // end if $num > 0
        return $groups;
    }

    function verify_user($username, $password) {
        $status = 0;
        $query = "select * from mdl_user where username='$username'";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            }
            $status = $id;
        } // end if $num > 0
        return $status;
    }

    function logout() {
        session_destroy();
    }

    function get_articles_archive() {
        $list = "";
        $query = "select * from mdl_course_modules "
                . "where module=$this->page_module_id order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = new stdClass();
                foreach ($row as $key => $value) {
                    $item->$key = $value;
                }
                $items[$row['added']] = $item;
            } // end while
        } // end if $num > 0
        ksort($items);
        $list.=$this->create_articles_archive($items);
        return $list;
    }

    function get_article_name($item) {
        
    }

    function create_articles_archive($items) {
        $list = "";

        if (count($items > 0)) {
            $list.="<table class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list.="<thead>";
            $list.="<tr>";
            $list.="<th>Name</th>";
            $list.="<th>Date Added</th>";
            $list.="</tr>";
            $list.="</thead>";
            $list.="<tbody>";
            foreach ($items as $item) {
                $title = $this->get_article_name($item);
                $link = "<a href='#' onClick='return false;'></a>";
                $list.="<tr>";
                $list.="<td></td>";
                $list.="<td></td>";
                $list.="</tr>";
            } // end foreach
            $list.="</tbody>";
            $list.="</table>";
        } // end if count($items>0)

        return $list;
    }

}
