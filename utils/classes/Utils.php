<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/mailer/vendor/PHPMailerAutoload.php';

class Utils2 {

    public $db;
    public $limit;

    function __construct() {
        $this->db = new pdo_db();
        $this->limit = 3;
    }

    // **************** Classes functionality ****************** 

    function get_classes_list() {
        $query = "select * from mdl_groups order by name limit 0, $this->limit";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            foreach ($row as $key => $value) {
                $item->$key = $value;
            } // end foreach
            $items[] = $item;
        } // end while
        $list = $this->create_classes_list_tab($items);
        return $list;
    }

    function create_classes_list_tab($groups, $headers = true) {
        $list = "";
        if (count($groups) > 0) {
            if ($headers) {
                $list.="<div class='container-fluid' style='font-weight:bold;'>";
                $list.="<div class='col-sm-3'>Class Name</div><div class='col-sm-1' style='text-align:center;'>Students#</div>";
                $list.="</div>";
            }
            $list.="<div id='classes_container'>";
            foreach ($groups as $group) {
                $num = $this->get_class_members_num($group->id);
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-3'>$group->name</div><div class='col-sm-1' style='text-align:center;'>$num</div>";
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
        //echo "Function page: " . $page . "<br>";
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

}
