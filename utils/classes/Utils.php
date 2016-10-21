<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/mailer/vendor/PHPMailerAutoload.php';

class Utils2 {

    public $db;
    public $limit;
    public $student_role;
    public $tutor_role;
    public $json_path;

    function __construct() {
        $this->db = new pdo_db();
        $this->limit = 3;
        $this->student_role = 5;
        $this->tutor_role = 4;
        $this->json_path = $_SERVER['DOCUMENT_ROOT'] . '/lms/utils/data';
    }

    // **************** Classes functionality ****************** 

    function get_classes_list($headers = true) {
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
            $list = $this->create_classes_list_tab($items, $headers);
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
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-5' id='ajax' style='display:none;'><img src='../../assets/images/ajax.gif'></div>";
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

    function search_class($item) {
        $items = array();
        $query = "select * from mdl_groups where name='$item'";
        //echo "Query: " . $query . "<br>";
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

    function get_tutors_list($headers = true) {
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
        $list = $this->create_tutors_list_tab($items, $headers);
        return $list;
    }

    function create_tutors_list_tab($items, $headers = true) {
        $list = "";
        if (count($items) > 0) {
            //echo "Inside count ++<br>";
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
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-7' id='ajax_tutor' style='display:none;'><img src='../../assets/images/ajax.gif'></div>";
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

    function get_user_id_by_fio($firstname, $lastname) {
        $users = array();
        $query = "select * from mdl_user "
                . "where firstname like '%$firstname%' "
                . "and lastname like '%$lastname%'";
        //echo "Query: " . $query . "<br>";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $users[] = $row['id'];
            } // end while
        } // end if $num > 0
        return $users;
    }

    function search_tutor($item) {
        $list = "";
        $items = array();
        $data_arr = explode(' ', $item);
        $firstname = $data_arr[1];
        $lastname = $data_arr[0];
        $query = "select * from mdl_user "
                . "where firstname like '%$firstname%' "
                . "and lastname like '%$lastname%' and deleted=0";
        //echo "Query: " . $query . "<br>";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $tutor = new stdClass();
                foreach ($row as $key => $value) {
                    $tutor->$key = $value;
                }
                $tutor->userid = $row['id'];
                $items[] = $tutor;
            } // end while
            $list.=$this->create_tutors_list_tab($items, false);
        } // end if $num > 0
        else {
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-2'>N/A</div>";
            $list.="</div>";
        }
        return $list;
    }

    // **************** Subscription functionality ****************** 

    function get_subscription_list($headers = true) {
        $items = array();
        $query = "select * from mdl_card_payments "
                . "order by added desc limit 0, $this->limit";
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
        $list = $this->create_subscription_list($items, $headers);
        return $list;
    }

    function create_subscription_list($items, $headers = true) {
        $list = "";
        if (count($items) > 0) {
            /*
              echo "<pre>";
              print_r($items);
              echo "</pre>";
             */

            if ($headers) {
                $list.="<div class='container-fluid' style='font-weight:bold;'>";
                $list.="<div class='col-sm-2' style='text-align:left;'>Student</div><div class='col-sm-2'>Class Name</div><div class='col-sm-3' style='text-align:center;'>Key</div><div class='col-sm-2' style='text-align:center;'>Start date</div><div class='col-sm-2' style='text-align:center;'>Expiration date</div><div class='col-sm-1' style='text-align:center;'>Action</div>";
                $list.="</div>";
            }
            $list.="<div id='subs_container'>";
            foreach ($items as $item) {
                $user = $this->get_user_detailes($item->userid);
                $class = $this->get_group_name($item->groupid);
                $start = date('m-d-Y', $item->start_date);
                $exp = date('m-d-Y', $item->exp_date);
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-2'>$user->firstname $user->lastname</div><div class='col-sm-2'>$class</div><div class='col-sm-3'>$item->auth_key</div><div class='col-sm-2' style='text-align:center;'>$start</div><div class='col-sm-2' style='text-align:center;'>$exp</div><div class='col-sm-1' style='text-align:center;'><a href='#' onClick='return false;' class='adjust' data-userid='$item->userid' data-groupid='$item->groupid'>Adjust</a></div>";
                $list.="</div>";
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-14'><hr/></div>";
                $list.="</div>";
            } // end foreach
            $list.="</div>";
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-14' id='ajax_subs' style='display:none;'><img src='../../assets/images/ajax.gif'></div>";
            $list.="</div>";
            if ($headers) {
                $list.="<br><div class='container-fluid'>";
                $list.="<div class='col-sm-9' id='subs_paginator'></div>";
                $list.="</div>";
            }
        } // end if count($items)>0
        else {
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-2'>N/A</div>";
            $list.="</div>";
        } // end else
        return $list;
    }

    function get_total_subscription() {
        $query = "select count(id) as total from mdl_card_payments";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }
        return $total;
    }

    function get_subscritpion_item($page) {
        $items = array();
        if ($page == 1) {
            $offset = 0;
        } // end if $page==1
        else {
            $page = $page - 1;
            $offset = $this->limit * $page;
        }
        $query = "select * from mdl_card_payments  "
                . "order by added desc "
                . "LIMIT $offset, $this->limit";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            foreach ($row as $key => $value) {
                $item->$key = $value;
            } // end foreach      
            $items[] = $item;
        } // end while
        $list = $this->create_subscription_list($items, false);
        return $list;
    }

    function search_subs($data) {
        $list = "";
        $items = array();
        $data_arr = explode(' ', $data);
        $firstname = $data_arr[1];
        $lastname = $data_arr[0];
        $users_array = $this->get_user_id_by_fio($firstname, $lastname);
        if (count($users_array) > 0) {
            $users_list = implode(",", $users_array);
            $query = "select * from mdl_card_payments where userid in ($users_list)";
            // echo "Query: " . $query . "<br>";
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
                $list.=$this->create_subscription_list($items, false);
            } // end if $num > 0
            else {
                $list.="<div class='container-fluid' style='text-align:center;'>";
                $list.="<div class='col-sm-2'>N/A</div>";
                $list.="</div>";
            } // end else
        } // end if count($users_array) > 0
        else {
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-2'>N/A</div>";
            $list.="</div>";
        }
        return $list;
    }

    function search_trial($data) {
        $list = "";
        $items = array();
        $data_arr = explode(' ', $data);
        $firstname = $data_arr[1];
        $lastname = $data_arr[0];
        $users_array = $this->get_user_id_by_fio($firstname, $lastname);
        if (count($users_array) > 0) {
            $users_list = implode(",", $users_array);
            $query = "select * from mdl_trial_keys where userid in ($users_list)";
            // echo "Query: " . $query . "<br>";
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
                $list.=$this->create_keys_list_tab($items, false);
            } // end if $num > 0
            else {
                $list.="<div class='container-fluid' style='text-align:center;'>";
                $list.="<div class='col-sm-2'>N/A</div>";
                $list.="</div>";
            } // end else
        } // end if count($users_array) > 0
        else {
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-2'>N/A</div>";
            $list.="</div>";
        }
        return $list;
    }

    // **************** Trial keys functionality ****************** 

    function get_trial_keys_tab($header = true) {
        $items = array();
        $query = "select * from mdl_trial_keys order by added limit 0, $this->limit";
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
            $list = $this->create_keys_list_tab($items, $header);
        }
        return $list;
    }

    function create_keys_list_tab($items, $headers) {
        $list = "";
        if (count($items) > 0) {

            if ($headers) {
                $list.="<div class='container-fluid' style='font-weight:bold;'>";
                $list.="<div class='col-sm-2' style='text-align:left;'>Student</div><div class='col-sm-2'>Class Name</div><div class='col-sm-3' style='text-align:center;'>Key</div><div class='col-sm-2' style='text-align:center;'>Start date</div><div class='col-sm-2' style='text-align:center;'>Expiration date</div>";
                $list.="</div>";
            }
            $list.="<div id='trial_container'>";
            foreach ($items as $item) {
                $user = $this->get_user_detailes($item->userid);
                $class = $this->get_group_name($item->groupid);
                $start = date('m-d-Y', $item->start_date);
                $exp = date('m-d-Y', $item->exp_date);
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-2'>$user->firstname $user->lastname</div><div class='col-sm-2'>$class</div><div class='col-sm-3'>$item->auth_key</div><div class='col-sm-2' style='text-align:center;'>$start</div><div class='col-sm-2' style='text-align:center;'>$exp</div>";
                $list.="</div>";
                $list.="<div class='container-fluid'>";
                $list.="<div class='col-sm-14'><hr/></div>";
                $list.="</div>";
            } // end foreach
            $list.="</div>";
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-14' id='ajax_trial' style='display:none;'><img src='../../assets/images/ajax.gif'></div>";
            $list.="</div>";
            if ($headers) {
                $list.="<br><div class='container-fluid'>";
                $list.="<div class='col-sm-9' id='trial_paginator'></div>";
                $list.="</div>";
            }
        } // end if count($items)>0
        else {
            $list.="<div class='container-fluid' style='text-align:center;'>";
            $list.="<div class='col-sm-2'>N/A</div>";
            $list.="</div>";
        } // end else
        return $list;
    }

    function get_trial_total() {
        $query = "select count(id) as total "
                . "from mdl_trial_keys ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }
        return $total;
    }

    function get_trial_item($page) {
        $items = array();
        if ($page == 1) {
            $offset = 0;
        } // end if $page==1
        else {
            $page = $page - 1;
            $offset = $this->limit * $page;
        }
        $query = "select * from mdl_trial_keys  "
                . "LIMIT $offset, $this->limit";
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

    // **************** Typehead block ****************** 

    function create_json_data($item) {
        switch ($item) {
            case "class":
                $query = "select * from mdl_groups order by name";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = mb_convert_encoding(trim($row['name']), 'UTF-8');
                    } // end while
                    $path = $this->json_path . '/classes.json';
                    file_put_contents($path, json_encode($data));
                } // end if $num > 0 
                break;
            case "tutor":
                $query = "select u.id, u.firstname, u.lastname, u.policyagreed, "
                        . "r.roleid, r.userid from mdl_user u, mdl_role_assignments r "
                        . "where r.roleid=$this->tutor_role and u.id=r.userid ";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $firstname = mb_convert_encoding(trim($row['firstname']), 'UTF-8');
                        $lastname = mb_convert_encoding(trim($row['lastname']), 'UTF-8');
                        $data2[] = $lastname . " " . $firstname;
                    } // end while
                    $path = $this->json_path . '/tutors.json';
                    file_put_contents($path, json_encode($data2));
                } // end if $num > 0
                break;
            case"subs";
                $query = "select u.id, u.firstname, u.lastname, u.policyagreed, "
                        . "r.roleid, r.userid, p.userid "
                        . "from mdl_user u, mdl_role_assignments r, mdl_card_payments p "
                        . "where r.roleid=$this->student_role "
                        . "and u.id=r.userid "
                        . "and u.id=p.userid ";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $firstname = mb_convert_encoding(trim($row['firstname']), 'UTF-8');
                        $lastname = mb_convert_encoding(trim($row['lastname']), 'UTF-8');
                        $data3[] = $lastname . " " . $firstname;
                    } // end while
                    $path = $this->json_path . '/subs.json';
                    file_put_contents($path, json_encode($data3));
                }
                break;
            case "trial";
                $query = "select * from mdl_user where deleted=0";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $firstname = mb_convert_encoding(trim($row['firstname']), 'UTF-8');
                        $lastname = mb_convert_encoding(trim($row['lastname']), 'UTF-8');
                        $data4[] = $lastname . " " . $firstname;
                    } // end while
                    $path = $this->json_path . '/trial.json';
                    file_put_contents($path, json_encode($data4));
                }
                break;
        }
    }

    function get_search_block($item) {
        $list = "";

        switch ($item) {
            case "class":
                $this->create_json_data('class');
                $list.="<input type='text' id='search_class' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_class_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_class_button'>Clear</button>";
                break;
            case "tutor":
                $this->create_json_data('tutor');
                $list.="<input type='text' id='search_tutor' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_tutor_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_tutor_button'>Clear</button>";
                break;
            case"subs";
                $this->create_json_data('subs');
                $list.="<input type='text' id='search_subs' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_subs_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_subs_button'>Clear</button>";
                break;
            case "trial";
                $this->create_json_data('trial');
                $list.="<input type='text' id='search_trial' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_trial_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_trial_button'>Clear</button>&nbsp;<button type='submit' class='btn btn-default' id='add_trial_button'>Add trial key</button>";
                break;
        }
        return $list;
    }

    /*     * *********************** Adjustments **************************** */

    function get_adjust_dialog($userid, $groupid) {
        $list = "";
        $query = "select * from mdl_card_payments "
                . "where userid=$userid and groupid=$groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $unix_start_date = $row['start_date'];
            $unix_exp_date = $row['exp_date'];
        }

        //echo "Unix start date: " . $unix_start_date . "<br>";
        //echo "Unix exp date: " . $unix_exp_date . "<br>";

        $start = date('m/d/Y', $unix_start_date);
        $end = date('m/d/Y', $unix_exp_date);
        $list.="<!-- Trigger the modal with a button -->
       
            <!-- Modal -->
            <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>
                    <h4 class='modal-title'>Add trial key</h4>
                  </div>
                  <div class='modal-body'>
                    
                    <input type='hidden' id='userid' value='$userid'>
                    <input type='hidden' id='groupid' value='$groupid'>
                        
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Start date</div>
                    <div class='col-sm-2'><input type='text' id='subs_start' value='$start'></div>
                    </div>
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Expiration date</div>
                    <div class='col-sm-2'><input type='text' id='subs_exp' value='$end'></div>
                    </div>

                    <div class='container-fluid'>
                    <div class='col-sm-6' id='subs_err' style='color:red;'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='modal_ok'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='modal_cancel'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    function get_add_trial_key_dialog() {
        $list.="<!-- Trigger the modal with a button -->
       
            <!-- Modal -->
            <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Adjust subscription</h4>
                  </div>
                  <div class='modal-body' style='text-align:center;'>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-3'>User*</div>
                    <div class='col-sm-2'><input type='text' id='trial_user'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-3'>Class*</div>
                    <div class='col-sm-2'><input type='text' id='trial_class'></div>
                    </div>
                    
                    <div class='container-fluid'>
                    <div class='col-sm-6' id='subs_err' style='color:red;'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='trial_ok'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='modal_cancel'>Close</button>
                  </div>
                </div>

              </div>
            </div>";
        return $list;
    }

    function get_group_id($name) {
        $query = "select * from mdl_groups where name='$name'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }

    function generateRandomString($length = 25) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    function add_trial_key($username, $groupname) {

        $unix_start = time();
        $unix_exp = $unix_start + 259200; // tree days later
        $names = explode(" ", $username);
        $firstname = $names[1];
        $lastname = $names[0];
        $courseid = 2;
        $groupid = $this->get_group_id($groupname);
        $key = $this->generateRandomString();
        $now = time();
        $users = $this->get_user_id_by_fio($firstname, $lastname); // array
        if (count($users) > 0) {
            foreach ($users as $userid) {
                $query = "insert into mdl_trial_keys "
                        . "(userid,"
                        . "courseid,"
                        . "groupid,"
                        . "auth_key,"
                        . "start_date,"
                        . "exp_date,"
                        . "valid,"
                        . "added) "
                        . "values($userid,"
                        . "$courseid,"
                        . "$groupid,"
                        . "'$key',"
                        . "'$unix_start',"
                        . "'$unix_exp'"
                        . ",1,"
                        . "'$now')";
                //echo "Query: " . $query . "<br>";
                $this->db->query($query);
            } // end foreach 
        } // end if count($users)>0
    }

    function adjust_subs($userid, $groupid, $start, $exp) {
        $unix_start = strtotime($start);
        $unix_exp = strtotime($exp);
        $query = "update mdl_card_payments set "
                . "start_date='$unix_start', "
                . "exp_date='$unix_exp' "
                . "where userid=$userid and groupid=$groupid";
        $this->db->query($query);
    }

    function logout() {
        session_destroy();
    }

}
