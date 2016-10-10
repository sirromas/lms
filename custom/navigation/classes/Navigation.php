<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Navigation extends Utils {

    public $page_module_id = 15;
    public $assesment_module_id = 1;
    public $forum_module_id = 9;
    public $quiz_module_id = 16;

    function __construct() {
        parent::__construct();
    }

    function get_assesment_link() {
        
    }

    function get_page_link() {
        $pageid = 0;
        $usergroups = $this->get_user_groups();
        $query = "select * from mdl_course_modules "
                . "where module=$this->page_module_id "
                . "and visible=1 order by added desc limit 0,1";
        $num = $this->db->numrows($query);
        if ($num > 0) {
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

    function get_forum_link() {
        
    }

    function get_quiz_link() {
        
    }

}
