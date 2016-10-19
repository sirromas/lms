<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/mailer/vendor/PHPMailerAutoload.php';

class Utils2 {

    public $db;
    public $limit;
    public $student_role;
    public $tutor_role;

    function __construct() {
        $this->db = new pdo_db();
        $this->limit = 3;
        $this->student_role = 5;
        $this->tutor_role = 4;
    }

    // **************** Classes functionality ****************** 

    function get_classes_list() {
        $items = array();
        $query = "select * from mdl_groups order by name limit 0, $this->limit";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = new stdClass();
                foreach ($row as $key => $value) {
                    $item->$key = $value;
                } // end foreach
                $items[] = $item;
            } // end while
            $list = $this->create_classes_list_tab($items);
        }
        return $list;
    }

    function create_classes_list_tab($groups, $headers = true) {
        $list = "";
        if (count($groups) > 0) {
            if ($headers) {
                $list.="<div class='container-fluid' style='font-weight:bold;'>";
                $list.="<div class='col-sm-3'>Class Name</div><div class='col-sm-2' style='text-align:center;'>Students Num</div>";
                $list.="</div>";
            }
            $list.="<div id='classes_container'>";
            foreach ($groups as $group) {
                $num = $this->get_class_members_num($group->id);
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-3'>$group->name</div><div class='col-sm-2' style='text-align:center;'>$num</div>";
                $list.="</div>";
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-5'><hr/></div>";
                $list.="</div>";
            } // end foreach
            $list.="</div>";
            if ($headers) {
                $list.="<br><div class='container-fluid'>";
                $list.="<div class='col-sm-9' id='class_paginator'></div>";
                $list.="</div>";
            }
        } // end if count($groups)>0
        else {
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='span8'>N/A</div>";
            $list.="</div>";
        } // end else
        return $list;
    }

    function get_classes_num() {
        $query = "select count(id) as total from mdl_groups";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }
        return $total;
    }

    function get_class_members_num($id) {
        $query = "select count(id) as total from mdl_groups_members "
                . "where groupid=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }
        return $total;
    }

    function get_classes_item($page) {
        $items = array();
        if ($page == 1) {
            $offset = 0;
        } // end if $page==1
        else {
            $page = $page - 1;
            $offset = $this->limit * $page;
        }
        $query = "select * from mdl_groups  "
                . "order by name "
                . "LIMIT $offset, $this->limit";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            foreach ($row as $key => $value) {
                $item->$key = $value;
            } // end foreach      
            $items[] = $item;
        } // end while
        $list = $this->create_classes_list_tab($items, false);
        return $list;
    }

    // **************** Tutors functionality ****************** 

    function get_user_detailes($userid) {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            foreach ($row as $key => $value) {
                $item->$key = $value;
            } // end foreach
        } // end while
        return $item;
    }

    function get_group_name($id) {
        $query = "select * from mdl_groups where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }
        return $name;
    }

    function get_user_groups($userid) {
        $list = "";
        $groups = array();
        $query = "select * from mdl_groups_members where userid=$userid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $groups[] = $row['groupid'];
            } // end while
            foreach ($groups as $groupid) {
                $groupname = $this->get_group_name($groupid);
                $list.="$groupname<br>";
            }
        } // end if $num > 0
        else {
            $list.="<div class='container-fluid'>";
            $list.="<div class='col-sm-3'>N/A</div>";
            $list.="</div>";
        } // end else
        return $list;
    }

    function get_tutors_list() {
        $items = array();
        $query = "select u.id, u.firstname, u.lastname, u.policyagreed, "
                . "r.roleid, r.userid from mdl_user u, mdl_role_assignments r "
                . "where r.roleid=$this->tutor_role and u.id=r.userid "
                . "limit 0, $this->limit";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = new stdClass();
                foreach ($row as $key => $value) {
                    $item->$key = $value;
                } // end foreach
                $items[] = $item;
            } // end while
        } // end if $num > 0
        $list = $this->create_tutors_list_tab($items);
        return $list;
    }

    function create_tutors_list_tab($items, $headers = true) {
        $list = "";
        if (count($items) > 0) {
            if ($headers) {
                $list.="<div class='container-fluid' style='font-weight:bold;'>";
                $list.="<div class='col-sm-2' style='text-align:left;'>Professor</div><div class='col-sm-2'>Class Name</div><div class='col-sm-3' style='text-align:center;'>Status</div>";
                $list.="</div>";
            }
            $list.="<div id='tutors_container'>";
            foreach ($items as $item) {
                $user = $this->get_user_detailes($item->userid);
                $groups = $this->get_user_groups($item->userid);
                $status = ($user->policyagreed == 1) ? "Confirmed" : "Not confirmed&nbsp;<a href='#' class='confirm' onClick='return false;' data-userid='$item->userid'>Confrm</a>";
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-2'>$user->firstname $user->lastname</div><div class='col-sm-2'>$groups</div><div class='col-sm-3' style='text-align:center;'>$status</div>";
                $list.="</div>";
                $list.="<div class='container-fluid' style='text-align:center;'>";
                $list.="<div class='col-sm-7' style='text-align:center;'><hr/></div>";
                $list.="</div>";
            } // end foreach
            $list.="</div>";
            if ($headers) {
                $list.="<br><div class='container-fluid'>";
                $list.="<div class='col-sm-9' id='tutors_paginator'></div>";
                $list.="</div>";
            }
        } // end if count($groups)>0
        else {
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-2'>N/A</div>";
            $list.="</div>";
        } // end else
        return $list;
    }

    function get_total_tutors_number() {
        $query = "select count(id) as total "
                . "from mdl_role_assignments "
                . "where roleid=$this->tutor_role";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }
        return $total;
    }

    function get_tutor_item($page) {
        $items = array();
        if ($page == 1) {
            $offset = 0;
        } // end if $page==1
        else {
            $page = $page - 1;
            $offset = $this->limit * $page;
        }
        $query = "select u.id, u.firstname, u.lastname, u.policyagreed, "
                . "r.roleid, r.userid from mdl_user u, mdl_role_assignments r "
                . "where r.roleid=$this->tutor_role and u.id=r.userid "
                . "limit $offset, $this->limit";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            foreach ($row as $key => $value) {
                $item->$key = $value;
            } // end foreach      
            $items[] = $item;
        } // end while
        $list = $this->create_tutors_list_tab($items, false);
        return $list;
    }

    function confirm_tutor($userid) {
        $query = "update mdl_user set policyagreed=1 where id=$userid";
        $this->db->query($query);
    }

}
