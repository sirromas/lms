<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';

/**
 * Description of Archive
 *
 * @author moyo
 * 
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

    /* *********************************************************************
     * 
     *                      Common section
     * 
     * ******************************************************************** */

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

    function verify_user($username) {
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

    /* *********************************************************************
     * 
     *                      Arcticles section
     * 
     * ******************************************************************** */

    function get_articles_archive() {
        $list = "";
        $items = array();
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
                $items[] = $item;
            } // end while
        } // end if $num > 0
        $list.=$this->create_articles_archive($items);
        return $list;
    }

    function get_article_name($item) {
        $query = "select * from mdl_page where id=$item->instance";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }
        return $name;
    }

    function create_articles_archive($items) {
        $list = "";
        if (count($items > 0)) {
            $list.="<br><br><table id='news_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list.="<thead>";
            $list.="<tr>";
            $list.="<th>Name</th>";
             $list.="<th>Link</th>";
            $list.="<th>Date Added</th>";
            $list.="</tr>";
            $list.="</thead>";
            $list.="<tbody>";
            foreach ($items as $item) {
                $title = $this->get_article_name($item);
                $link = "<a href='http://www.newsfactsandanalysis.com/lms/mod/page/view.php?id=$item->id' target='_blank'>link</a>";
                $date = date('m-d-Y', $item->added);
                $list.="<tr>";
                $list.="<td>$title</td>";
                 $list.="<td>$link</td>";
                $list.="<td>$date</td>";
                $list.="</tr>";
            } // end foreach
            $list.="</tbody>";
            $list.="</table>";
        } // end if count($items>0)
        return $list;
    }

    /* *********************************************************************
     * 
     *                      Forums section
     * 
     * ******************************************************************** */

    function get_forums_archive() {
        $list = "";
        $items = array();
        $query = "select * from mdl_course_modules "
                . "where module=$this->forum_module_id order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = new stdClass();
                foreach ($row as $key => $value) {
                    $item->$key = $value;
                }
                $items[] = $item;
            } // end while
        } // end if $num > 0
        $list.=$this->create_forum_archive($items);
        return $list;
    }

    function get_forum_name($item) {
        $query = "select * from mdl_forum where id=$item->instance";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }
        return $name;
    }

    function create_forum_archive($items) {
        $list = "";
        if (count($items > 0)) {
            $list.="<br><br><table id='forum_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list.="<thead>";
            $list.="<tr>";
            $list.="<th>Name</th>";
            $list.="<th>Link</th>";
            $list.="<th>Date Added</th>";
            $list.="</tr>";
            $list.="</thead>";
            $list.="<tbody>";
            foreach ($items as $item) {
                $title = $this->get_forum_name($item);
                $link = "<a href='http://www.newsfactsandanalysis.com/lms/mod/forum/view.php?id=$item->id' target='_blank'>link</a>";
                $date = date('m-d-Y', $item->added);
                $list.="<tr>";
                $list.="<td>$title</td>";
                $list.="<td>$link</td>";
                $list.="<td>$date</td>";
                $list.="</tr>";
            } // end foreach
            $list.="</tbody>";
            $list.="</table>";
        } // end if count($items>0)
        return $list;
    }

    /* *********************************************************************
     * 
     *                      Quiz section
     * 
     * ******************************************************************** */

    function get_quiz_archive() {
        $list = "";
        $items = array();
        $query = "select * from mdl_course_modules "
                . "where module=$this->quiz_module_id order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = new stdClass();
                foreach ($row as $key => $value) {
                    $item->$key = $value;
                }
                $items[] = $item;
            } // end while
        } // end if $num > 0
        $list.=$this->create_quiz_archive($items);
        return $list;
    }

    function get_quiz_name($item) {
        $query = "select * from mdl_quiz where id=$item->instance";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }
        return $name;
    }

    function create_quiz_archive($items) {
        $list = "";
        if (count($items > 0)) {
            $list.="<br><br><table id='quiz_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list.="<thead>";
            $list.="<tr>";
            $list.="<th>Name</th>";
             $list.="<th>Link</th>";
            $list.="<th>Date Added</th>";
            $list.="</tr>";
            $list.="</thead>";
            $list.="<tbody>";
            foreach ($items as $item) {
                $title = $this->get_quiz_name($item);
                $link = "<a href='http://www.newsfactsandanalysis.com/lms/mod/quiz/view.php?id=$item->id' target='_blank'>link</a>";
                $date = date('m-d-Y', $item->added);
                $list.="<tr>";
                $list.="<td>$title</td>";
                $list.="<td>$link</td>";
                $list.="<td>$date</td>";
                $list.="</tr>";
            } // end foreach
            $list.="</tbody>";
            $list.="</table>";
        } // end if count($items>0)
        return $list;
    }

}
