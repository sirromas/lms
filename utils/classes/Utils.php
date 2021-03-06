<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/mailer/vendor/PHPMailerAutoload.php';

class Utils2
{

    public $db;
    public $limit;
    public $student_role;
    public $tutor_role;
    public $json_path;
    public $from_address_alredy_used;

    function __construct()
    {
        $this->db = new pdo_db();
        $this->limit = 3;
        $this->student_role = 5;
        $this->tutor_role = 4;
        $this->json_path = $_SERVER['DOCUMENT_ROOT'] . '/lms/utils/data';
        $this->from_address_alredy_used = 0;
    }

    // **************** Classes functionality ******************

    /**
     * @param $userid
     *
     * @return mixed
     */
    function get_user_role($userid)
    {
        $query = "select * from  mdl_role_assignments where userid=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $roleid = $row['roleid'];
        }

        return $roleid;
    }

    /**
     * @param $login
     * @param $password
     *
     * @return int
     */
    function authorize($login, $password)
    {
        $this->create_json_data('article');
        $this->create_json_data('groups');
        $query = "select * from mdl_admin_login "
            . "where username='$login' and password='$password'";
        $num = $this->db->numrows($query);

        return $num;
    }

    /**
     * @param bool $headers
     *
     * @return string
     */
    function get_classes_list($headers = true)
    {
        $items = array();
        $query = "select * from mdl_groups order by name";
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
        $list = $this->create_classes_list_tab($items, $headers);

        return $list;
    }

    /**
     * @param      $groups
     * @param bool $headers
     *
     * @return string
     */
    function create_classes_list_tab($groups, $headers = true)
    {
        $list = "";

        $list .= "<br><br><table id='classes_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Class Name</th>";
        $list .= "<th>Students Num</th>";
        $list .= "</tr>";
        $list .= "</thead>";
        $list .= "<tbody>";

        if (count($groups) > 0) {

            foreach ($groups as $group) {
                $num = $this->get_class_members_num($group->id);
                if ($group->name != '') {
                    $list .= "<tr>";
                    $list .= "<td>$group->name</td>";
                    $list .= "<td>$num</td>";
                    $list .= "</tr>";
                } // end if $group->name!='' && $num>0
            } // end foreach

        } // end if count($groups)>0

        $list .= "</tbody>";
        $list .= "</table>";

        return $list;
    }

    /**
     * @return mixed
     */
    function get_classes_num()
    {
        $query = "select count(id) as total from mdl_groups";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }

        return $total;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_class_members_num($id)
    {
        $query = "select count(id) as total from mdl_groups_members "
            . "where groupid=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }

        return $total;
    }

    /**
     * @param $page
     *
     * @return string
     */
    function get_classes_item($page)
    {
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

    /**
     * @param $item
     *
     * @return string
     */
    function search_class($item)
    {
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

    /**
     * @param $userid
     *
     * @return stdClass
     */
    function get_user_detailes($userid)
    {
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

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_group_name($id)
    {
        $name = '';
        if ($id > 0) {
            $query = "select * from mdl_groups where id=$id";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $name = $row['name'];
            }
        }

        return $name;
    }

    /**
     * @param $userid
     *
     * @return string
     */
    function get_user_groups($userid)
    {
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
                $list .= "$groupname<br>";
            }
        } // end if $num > 0
        else {
            $list .= "<div class='container-fluid'>";
            $list .= "<div class='col-sm-3'>N/A</div>";
            $list .= "</div>";
        } // end else

        return $list;
    }

    /**
     * @param $tutors
     */
    function create_csv_file($tutors)
    {
        // Write CSV data
        $path = $this->json_path . '/tutors.csv';
        $output = fopen($path, 'w');
        fputcsv($output, array('Firstname', 'Lastname', 'Email'));
        foreach ($tutors as $tutor) {
            fputcsv($output,
                array($tutor->firstname, $tutor->lastname, $tutor->email));
        }
        fclose($output);
    }

    /**
     * @param bool $headers
     *
     * @return string
     */
    function get_tutors_list($headers = true)
    {
        $items = array();
        $query
            = "select u.id, u.firstname, u.lastname, u.policyagreed, u.deleted, u.email, "
            . "r.roleid, r.userid from mdl_user u, mdl_role_assignments r "
            . "where u.deleted=0 and r.roleid=$this->tutor_role and u.id=r.userid ";
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
            $this->create_csv_file($items);
        } // end if $num > 0
        $list = $this->create_tutors_list_tab($items, $headers);

        return $list;
    }


    function is_assistant($userid)
    {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $parent = $row['parent'];
        }
        return $parent;
    }


    /**
     * @param      $items
     * @param bool $headers
     *
     * @return string
     */
    function create_tutors_list_tab($items, $headers = true)
    {
        $list = "";
        if (count($items) > 0) {
            $list .= "<br><br><table id='tutors_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th>Professor</th>";
            $list .= "<th>Class Name</th>";
            $list .= "<th>Status</th>";
            $list .= "</tr>";
            $list .= "</thead>";
            $list .= "<tbody>";
            foreach ($items as $item) {
                $user = $this->get_user_detailes($item->userid);
                $groups = $this->get_user_groups($item->userid);
                $is_assistant = $this->is_assistant($item->userid);
                $status = ($user->policyagreed == 1) ? "Confirmed" : "Not confirmed&nbsp;<a href='#' class='confirm' onClick='return false;' data-userid='$item->userid'>Confrm</a>";
                $list .= "<tr>";
                if ($is_assistant == 0) {
                    $list .= "<td>$user->firstname $user->lastname<br>u: $user->email<br>p: $user->purepwd</td>";
                } // end if
                else {
                    $tutor_data = $this->get_user_detailes($is_assistant);
                    $list .= "<td>$user->firstname $user->lastname
                    <br>u: $user->email<br>p: $user->purepwd<br> Assistant of $tutor_data->firstname $tutor_data->lastname</td>";
                }
                $list .= "<td>$groups</td>";
                $list .= "<td>$status</td>";
                $list .= "</tr>";
            } // end foreach
            $list .= "</tbody>";
            $list .= "</table>";
        } // end if count($groups)>0
        else {
            $list .= "<br><br><div class='container-fluid' style='text-align:left;'>";
            $list .= "<div class='span1'>N/A</div>";
            $list .= "</div>";
        } // end else

        return $list;
    }

    /**
     * @return mixed
     */
    function get_total_tutors_number()
    {
        $query = "select count(id) as total "
            . "from mdl_role_assignments "
            . "where roleid=$this->tutor_role";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }

        return $total;
    }

    /**
     * @param $page
     *
     * @return string
     */
    function get_tutor_item($page)
    {
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

    /**
     * @param $userid
     */
    function confirm_tutor($userid)
    {
        $query = "update mdl_user set policyagreed=1 where id=$userid";
        $this->db->query($query);
    }

    /**
     * @param $firstname
     * @param $lastname
     *
     * @return array
     */
    function get_user_id_by_fio($firstname, $lastname)
    {
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

    /**
     * @param $item
     *
     * @return string
     */
    function search_tutor($item)
    {
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
            $list .= $this->create_tutors_list_tab($items, false);
        } // end if $num > 0
        else {
            $list .= "<div class='container-fluid' style='text-align:center;'>";
            $list .= "<div class='col-sm-2'>N/A</div>";
            $list .= "</div>";
        }

        return $list;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function is_user_deleted($id)
    {
        $query = "select * from mdl_user where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $deleted = $row['deleted'];
        }

        return $deleted;
    }

    // **************** Subscription functionality ******************

    /**
     * @param bool $headers
     *
     * @return string
     */
    function get_subscription_list($headers = true)
    {
        $items = array();
        $query = "select * from mdl_card_payments "
            . "order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $status = $this->is_user_deleted($row['userid']);
                if ($status == 0) {
                    $item = new stdClass();
                    foreach ($row as $key => $value) {
                        $item->$key = $value;
                    } // end foreach
                    $items[] = $item;
                } // end if $status == 0
            } // end while
        } // end if $num > 0
        $list = $this->create_subscription_list($items, $headers);

        return $list;
    }

    /**
     * @return string
     */
    function get_paid_keys()
    {
        $list = $this->get_subscription_list();

        return $list;
    }


    /**
     * @return string
     */
    function get_from_emails_block()
    {
        $list = "";
        $list .= "<br><br><div class='row' style='text-align: left;font-weight: bold;'>";
        $list .= "<span class='col-md-12'>Emails used to send confirmation emails ('From' address)</span>";
        $list .= "</div>";

        $query = "select * from mdl_from_addr";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $emails[] = $row['email'];
        }

        $teacher_email = $emails[0];
        $student_email = $emails[1];

        $list .= "<div class='row' style='text-align: left;'>";
        $list .= "<span class='col-md-2'>For professors</span><span class='col-md-2'><input type='text' id='professor_from' value='$teacher_email'></span><span class='col-md-2'><button class='update_from_email' id='update_professor_from_btn'>Update</button></span>";
        $list .= "</div>";

        $list .= "<div class='row' style='text-align: left;'>";
        $list .= "<span class='col-md-2'>For students</span><span class='col-md-2'><input type='text' id='student_from' value='$student_email'></span><span class='col-md-2'><button class='update_from_email' id='update_student_from_btn'>Update</button></span>";
        $list .= "</div>";
        return $list;
    }


    /**
     * @return string
     */
    function get_semestr_duration_block()
    {
        $list = "";
        $list .= "<br><div class='row' style='font-weight: bold;text-align: left;'>";
        $list .= "<span class='col-md-12'>Semestr date(s)</span>";
        $list .= "</div>";

        $query = "select * from mdl_semestr_duration";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $text_id = $row['item_text'];
            $btn_id = $row['item_text'] . '_btn';
            $list .= "<div class='row'>";
            $list .= "<span class='col-md-2'>" . $row['item_text'] . "</span>";
            $list .= "<span class='col-md-2'><input type='text' value='" . $row['item_value'] . "' id='$text_id'></span>";
            $list .= "<span class='col-md-2'><button class='update_semestr' data-text='$text_id' id='$btn_id'>Update</button></span>";
            $list .= "</div>";
        }

        return $list;

    }

    /**
     * @param $item
     */
    function update_semestr_date($item)
    {
        $query = "update mdl_semestr_duration set item_value='$item->item_value' 
          where item_text='$item->item_text'";
        $this->db->query($query);
    }


    /**
     * @param $item
     */
    function update_from_email($item)
    {
        $query = "update mdl_from_addr set email='$item->email' where roleid=$item->roleid";
        $this->db->query($query);
    }

    /**
     * @param      $items
     * @param bool $headers
     *
     * @return string
     */
    function create_subscription_list($items, $headers = true)
    {
        $list = "";
        $list .= $this->get_from_emails_block();
        $list .= $this->get_semestr_duration_block();

        if (count($items) > 0) {
            $list .= "<br><br><table id='subs_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th>Student</th>";
            $list .= "<th>Email</th>";
            $list .= "<th>Class Name</th>";
            $list .= "<th>Key</th>";
            $list .= "<th>Start Date</th>";
            $list .= "<th>Expiration Date</th>";
            $list .= "<th>Action</th>";
            $list .= "</tr>";
            $list .= "</thead>";
            $list .= "<tbody>";
            foreach ($items as $item) {
                $user = $this->get_user_detailes($item->userid);
                $class = $this->get_group_name($item->groupid);
                $start = date('m-d-Y', $item->start_date);
                $exp = date('m-d-Y', $item->exp_date);
                $list .= "<tr>";
                $list .= "<td>$user->firstname $user->lastname</td>";
                $list .= "<td>$user->email</td>";
                $list .= "<td>$class</td>";
                $list .= "<td>$item->auth_key</td>";
                $list .= "<td>$start</td>";
                $list .= "<td>$exp</td>";
                $list .= "<td><a href='#' onClick='return false;' class='adjust' data-userid='$item->userid' data-paymentid='$item->id' data-groupid='$item->groupid'>Adjust</a></td>";
                $list .= "</tr>";
            } // end foreach
            $list .= "</tbody>";
            $list .= "</table>";
        } // end if count($items)>0
        else {
            $list .= "<br><br><div class='container-fluid' style='text-align:left;'>";
            $list .= "<div class='span1'>N/A</div>";
            $list .= "</div>";
        } // end else

        return $list;
    }

    /**
     * @return mixed
     */
    function get_total_subscription()
    {
        $query = "select count(id) as total from mdl_card_payments";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }

        return $total;
    }

    /**
     * @param $page
     *
     * @return string
     */
    function get_subscritpion_item($page)
    {
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

    /**
     * @param $data
     *
     * @return string
     */
    function search_subs($data)
    {
        $list = "";
        $items = array();
        $data_arr = explode(' ', $data);
        $firstname = $data_arr[1];
        $lastname = $data_arr[0];
        $users_array = $this->get_user_id_by_fio($firstname, $lastname);
        if (count($users_array) > 0) {
            $users_list = implode(",", $users_array);
            $query
                = "select * from mdl_card_payments where userid in ($users_list)";
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
                $list .= $this->create_subscription_list($items, false);
            } // end if $num > 0
            else {
                $list .= "<div class='container-fluid' style='text-align:center;'>";
                $list .= "<div class='col-sm-2'>N/A</div>";
                $list .= "</div>";
            } // end else
        } // end if count($users_array) > 0
        else {
            $list .= "<div class='container-fluid' style='text-align:center;'>";
            $list .= "<div class='col-sm-2'>N/A</div>";
            $list .= "</div>";
        }

        return $list;
    }

    /**
     * @param $name
     *
     * @return array
     */
    function get_group_members($name)
    {
        $groupid = $this->get_group_id($name);
        $users = array();
        if ($groupid > 0) {
            $query = "select * from mdl_groups_members where groupid=$groupid";
            //echo "Query: " . $query . "<br>";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $users[] = $row['userid'];
                } // end while
            } // end if $num > 0
        } // end if $groupid > 0

        return $users;
    }

    /**
     * @param $data
     *
     * @return string
     */
    function search_trial($data)
    {
        $list = "";
        $items = array();
        $data_arr = explode(' ', $data);
        $firstname = $data_arr[1];
        $lastname = $data_arr[0];
        $group_users = $this->get_group_members($data);
        /*
          echo "Group users: <pre>";
          print_r($group_users);
          echo "</pre><br>";
         */
        $fio_users = $this->get_user_id_by_fio($firstname, $lastname);
        /*
          echo "FIO users: <pre>";
          print_r($fio_users);
          echo "</pre><br>";
         */
        $users_array = array_merge($group_users, $fio_users);
        /*
          echo "Megred array of users: <pre>";
          print_r($users_array);
          echo "</pre><br>";
         */
        $users_list = implode(",", $users_array);

        if (count($users_array) > 0) {
            if (count($group_users) == 0) {
                $query
                    = "select * from mdl_trial_keys where userid in ($users_list)";
            } // end if
            if (count($group_users) > 0) {
                $groupid = $this->get_group_id($data);
                $query
                    = "select * from mdl_trial_keys where groupid=$groupid ";
            } // end if
            //echo "Query: " . $query . "<br>";
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

                /*
                  echo "<pre>";
                  print_r($items);
                  echo "</pre><br>";
                 */
                $list .= $this->create_keys_list_tab($items, false);
            } // end if $num > 0
            else {
                $list .= "<div class='container-fluid' style='text-align:center;'>";
                $list .= "<div class='col-sm-2'>N/A</div>";
                $list .= "</div>";
            } // end else
        } // end if count($users_array) > 0
        else {
            $list .= "<div class='container-fluid' style='text-align:center;'>";
            $list .= "<div class='col-sm-2'>N/A</div>";
            $list .= "</div>";
        }

        return $list;
    }

    // **************** Trial keys functionality ******************

    /**
     * @param bool $header
     *
     * @return string
     */
    function get_trial_keys_tab($header = true)
    {
        $items = array();
        $query = "select * from mdl_trial_keys order by added";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $status = $this->is_user_deleted($row['userid']);
                if ($status == 0) {
                    $item = new stdClass();
                    foreach ($row as $key => $value) {
                        $item->$key = $value;
                    } // end foreach
                    $items[] = $item;
                } // end if $status==0
            } // end while
        } // end if $num > 0
        $list = $this->create_keys_list_tab($items, $header);

        return $list;
    }

    /**
     * @return string
     */
    function get_trial_keys()
    {
        $list = $this->get_trial_keys_tab();

        return $list;
    }

    /**
     * @param $items
     * @param $headers
     *
     * @return string
     */
    function create_keys_list_tab($items, $headers)
    {
        $item = 'trial';
        $this->create_json_data($item);
        $list = "";

        $list .= "<div class='row-fluid'>";
        $list .= "<span class='span3' style='padding-left:25px;'><br><button class='btn btn-default' id='add_trial_button'>Add Trial Key</button></span>";
        $list .= "</div>";

        if (count($items) > 0) {
            $list .= "<br><br><table id='trial_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th>Student</th>";
            $list .= "<th>Email</th>";
            // $list.="<th>Class Name</th>";
            $list .= "<th>Key</th>";
            $list .= "<th>Start Date</th>";
            $list .= "<th>Expiration Date</th>";
            $list .= "<th>Action</th>";
            $list .= "</tr>";
            $list .= "</thead>";
            $list .= "<tbody>";
            foreach ($items as $item) {
                $user = $this->get_user_detailes($item->userid);
                $class = $this->get_group_name($item->groupid);
                $start = date('m-d-Y', $item->start_date);
                $exp = date('m-d-Y', $item->exp_date);
                $list .= "<tr>";
                $list .= "<td>$user->firstname $user->lastname</td>";
                $list .= "<td>$user->email</td>";
                //$list.="<td>$class</td>";
                $list .= "<td>$item->auth_key</td>";
                $list .= "<td>$start</td>";
                $list .= "<td>$exp</td>";
                $list .= "<td><a href='#' onClick='return false;' class='trial_adjust' data-userid='$item->userid' data-groupid='$item->groupid'>Adjust</a></td>";
                $list .= "</tr>";
            } // end foreach
            $list .= "</tbody>";
            $list .= "</table>";
        } // end if count($items)>0
        else {
            $list .= "<br><br><div class='container-fluid' style='text-align:left;'>";
            $list .= "<div class='span1'>N/A</div>";
            $list .= "</div>";
        } // end else

        return $list;
    }

    /**
     * @return mixed
     */
    function get_trial_total()
    {
        $query = "select count(id) as total "
            . "from mdl_trial_keys ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }

        return $total;
    }

    /**
     * @param $page
     *
     * @return string
     */
    function get_trial_item($page)
    {
        $items = array();
        if ($page == 1) {
            $offset = 0;
        } // end if $page==1
        else {
            $page = $page - 1;
            $offset = $this->limit * $page;
        }
        $query = "select * from mdl_trial_keys  order by added "
            . "LIMIT $offset, $this->limit";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            foreach ($row as $key => $value) {
                $item->$key = $value;
            } // end foreach
            $items[] = $item;
        } // end while
        $list = $this->create_keys_list_tab($items, false);

        return $list;
    }


    // **************** Typehead block ******************

    /**
     * @param $item
     */
    function create_json_data($item)
    {
        switch ($item) {
            case "article":
                $query = "select * from mdl_article order by title";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $title = mb_convert_encoding(trim($row['title']),
                            'UTF-8');
                        $dates = mb_convert_encoding(trim($row['path']),
                            'UTF-8');
                        $data[] = $title . '&&&' . $dates;
                    }
                    $path = $this->json_path . '/articles.json';
                    file_put_contents($path, json_encode($data));
                }
                break;
            case "class":
                $query = "select * from mdl_groups order by name";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = mb_convert_encoding(trim($row['name']),
                            'UTF-8');
                    } // end while
                    $path = $this->json_path . '/classes.json';
                    file_put_contents($path, json_encode($data));
                } // end if $num > 0
                break;
            case "tutor":
                $query
                    = "select u.id, u.firstname, u.lastname, u.policyagreed, "
                    . "r.roleid, r.userid from mdl_user u, mdl_role_assignments r "
                    . "where r.roleid=$this->tutor_role and u.id=r.userid ";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $firstname
                            = mb_convert_encoding(trim($row['firstname']),
                            'UTF-8');
                        $lastname = mb_convert_encoding(trim($row['lastname']),
                            'UTF-8');
                        $data2[] = $lastname . " " . $firstname;
                    } // end while
                    $path = $this->json_path . '/tutors.json';
                    file_put_contents($path, json_encode($data2));
                } // end if $num > 0
                break;
            case "subs":
                $query
                    = "select u.id, u.firstname, u.lastname, u.policyagreed, "
                    . "r.roleid, r.userid, p.userid "
                    . "from mdl_user u, mdl_role_assignments r, mdl_card_payments p "
                    . "where r.roleid=$this->student_role "
                    . "and u.id=r.userid "
                    . "and u.id=p.userid ";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $firstname
                            = mb_convert_encoding(trim($row['firstname']),
                            'UTF-8');
                        $lastname = mb_convert_encoding(trim($row['lastname']),
                            'UTF-8');
                        $data3[] = $lastname . " " . $firstname;
                    } // end while
                    $path = $this->json_path . '/subs.json';
                    file_put_contents($path, json_encode($data3));
                }
                break;
            case "trial":
                $groups = array();
                $query = "select * from mdl_user where deleted=0";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $firstname
                            = mb_convert_encoding(trim($row['firstname']),
                            'UTF-8');
                        $lastname = mb_convert_encoding(trim($row['lastname']),
                            'UTF-8');
                        $users[] = $lastname . " " . $firstname;
                    } // end while

                    $query = "select * from mdl_groups order by name";
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $groups[] = mb_convert_encoding(trim($row['name']),
                            'UTF-8');
                    }

                    $path = $this->json_path . '/trial.json';
                    file_put_contents($path, json_encode($groups));

                    $path = $this->json_path . '/users.json';
                    file_put_contents($path, json_encode($users));
                }
                break;
            case 'groups':
                $groups = array();
                $query = "select * from mdl_groups order by name";
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $groups[] = mb_convert_encoding(trim($row['name']), 'UTF-8');
                }
                $path = $this->json_path . '/groups.json';
                if (count($groups) > 0) {
                    file_put_contents($path, json_encode($groups));
                }
                break;
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    function get_groupid_by_name($name)
    {
        $query = "select * from mdl_groups where name='$name'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }

        return $id;
    }

    /**
     * @param $item
     *
     * @return string
     */
    function get_search_block($item)
    {
        $list = "";

        /*
         *
          switch ($item) {
          case "class":
          $this->create_json_data('class');
          $list.="<input type='text' id='search_class' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_class_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_class_button'>Clear</button>";
          break;
          case "tutor":
          $this->create_json_data('tutor');
          $tutors_path = 'http://globalizationplus.com/lms/utils/data/tutors.csv';
          $list.="<input type='text' id='search_tutor' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_tutor_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_tutor_button'>Clear</button>&nbsp;<a href='$tutors_path' target='_blank'><button type='submit' class='btn btn-default' id='export_tutor_button'>Export</button></a>";
          break;
          case"subs";
          $this->create_json_data('subs');
          $list.="<input type='text' id='search_subs' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_subs_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_subs_button'>Clear</button>";
          break;
          case "trial";
          $this->create_json_data('trial');
          $list.="<input type='text' id='search_trial' class='typeahead'>&nbsp;<button type='submit' class='btn btn-default' id='search_trial_button'>Search</button>&nbsp;<button type='submit' class='btn btn-default' id='clear_trial_button'>Clear</button>&nbsp;<button type='submit' class='btn btn-default' id='add_trial_button'>Add trial key</button>&nbsp;<button type='submit' class='btn btn-default' id='adjust_trial_group'>Adjust</button>";
          break;
          }
         *
         */

        return $list;
    }

    /*     * *********************** Adjustments **************************** */

    /**
     * @param $userid
     * @param $groupid
     *
     * @return string
     */
    function get_adjust_dialog($userid, $groupid)
    {
        $list = "";
        $query = "select * from mdl_card_payments "
            . "where userid=$userid and groupid=$groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $unix_start_date = $row['start_date'];
            $unix_exp_date = $row['exp_date'];
        }

        //echo "Unix start date: " . $unix_start_date . "<br>";
        //echo "Unix exp date: " . $unix_exp_date . "<br>";

        $start = date('m/d/Y', $unix_start_date);
        $end = date('m/d/Y', $unix_exp_date);
        $list
            .= "<!-- Trigger the modal with a button -->
       
            <!-- Modal -->
            <div id='myModal_paid_$userid' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Adjust subscription</h4>
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
                    <button type='button' class='btn btn-default' id='modal_ok' data-paymentid='$id'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='modal_cancel_paid_$userid' data-userid='$userid'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @return string
     */
    function get_add_trial_key_dialog()
    {
        $list = "";
        $list
            .= "<!-- Trigger the modal with a button -->
       
            <!-- Modal -->
            <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Add trial key</h4>
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
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='modal_cancel_trial'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $name
     *
     * @return int
     */
    function get_group_id($name)
    {
        $id = 0;
        $query = "select * from mdl_groups where name='$name'";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            } // end while
        } // end if $num > 0

        return $id;
    }

    /**
     * @param int $length
     *
     * @return bool|string
     */
    function generateRandomString($length = 25)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),
            0, $length);
    }

    /**
     * @param $username
     * @param $groupname
     */
    function add_trial_key($username, $groupname)
    {

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

    /**
     * @param $subs
     */
    function adjust_subs($subs)
    {

        /*
          echo "<pre>";
          print_r($subs);
          echo "</pre>";
         */

        $unix_start = strtotime($subs->start);
        $unix_exp = strtotime($subs->exp);
        $query = "update mdl_card_payments set "
            . "start_date='$unix_start', "
            . "exp_date='$unix_exp' "
            . "where id=$subs->paymentid";
        //echo "Query: ".$query."<br>";

        $this->db->query($query);
    }

    /**
     * @param $users
     *
     * @return string
     */
    function get_group_modal_dialog($users)
    {
        $list = "";
        $endcoded_users = json_encode($users);
        $list
            .= "<!-- Trigger the modal with a button -->
       
            <!-- Modal -->
            <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Adjust trial key(s)</h4>
                  </div>
                  <div class='modal-body'>
                    
                    <input type='hidden' id='users' value='$endcoded_users'>
                        
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Start date</div>
                    <div class='col-sm-2'><input type='text' id='trial_start'></div>
                    </div>
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Expiration date</div>
                    <div class='col-sm-2'><input type='text' id='trial_exp'></div>
                    </div>

                    <div class='container-fluid'>
                    <div class='col-sm-6' id='subs_err' style='color:red;'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='group_modal_trial_ok'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='modal_cancel_trial'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $user
     *
     * @return string
     */
    function get_adjust_trial_personal_key_modal_dialog($user)
    {
        $list = "";

        $query = "select * from mdl_trial_keys "
            . "where userid=$user->userid and groupid=$user->groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $start = date('m-d-Y', $row['start_date']);
            $end = date('m-d-Y', $row['exp_date']);
        }

        $list
            .= "<!-- Trigger the modal with a button -->
       
            <!-- Modal -->
            <div id='myModal_trial_$user->userid' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Adjust trial key(s)</h4>
                  </div>
                  <div class='modal-body'>
                    
                    <input type='hidden' id='userid' value='$user->userid'>
                    <input type='hidden' id='groupid' value='$user->groupid'>
                     
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Start date</div>
                    <div class='col-sm-2'><input type='text' id='trial_start' value='$start'></div>
                    </div>
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Expiration date</div>
                    <div class='col-sm-2'><input type='text' id='trial_exp' value='$end'></div>
                    </div>

                    <div class='container-fluid'>
                    <div class='col-sm-6' id='subs_err' style='color:red;'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='personal_modal_trial_ok'>Ok</button>
                    <button type='button' data-userid='$user->userid' class='btn btn-default' data-dismiss='modal' id='cancel_trial_$user->userid'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $id
     *
     * @return string
     */
    function get_adjust_price_modal_dialog($id)
    {
        $list = "";

        $query = "select * from mdl_price where id=$id ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['institute'];
            $price = $row['price'];
        }

        $list
            .= "
            <!-- Modal -->
            <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h4 class='modal-title'>Adjust Price</h4>
                  </div>
                  <div class='modal-body'>
                    
                    <input type='hidden' id='id' value='$id'>
                     
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-3'>Schoolname</div>
                    <div class='col-sm-6'>$name</div>
                    </div>
                    <br><div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-3'>Price ($)</div>
                    <div class='col-sm-2'><input type='text' id='school_price' value='$price'></div>
                    </div>

                    <div class='container-fluid'>
                    <div class='col-sm-6' id='price_err' style='color:red;'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='update_school_price'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_trial_'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @return string
     */
    function get_add_new_school_modal_dialog()
    {
        $list = "";

        $list
            .= "
            <!-- Modal -->
            <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h4 class='modal-title'>Add New School</h4>
                  </div>
                  <div class='modal-body'>
                     
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-3'>Schoolname</div>
                    <div class='col-sm-6'><input type='text' id='name'></div>
                    </div>
                    <br><div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-3'>Price ($)</div>
                    <div class='col-sm-2'><input type='text' id='price'></div>
                    </div>

                    <div class='container-fluid'>
                    <div class='col-sm-12' id='price_err' style='color:red;'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='add_new_school_to_db'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_trial_'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @return string
     */
    function get_upload_price_csv_modal_dialog()
    {
        $list = "";

        $list
            .= "
            <!-- Modal -->
            <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h4 class='modal-title'>Upload schools CSV file</h4>
                  </div>
                  <div class='modal-body'>
                     
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-3'>Filename*</div>
                    <div class='col-sm-6'><input type='file' id='price_scv'></div>
                    </div>
                 
                    <div class='container-fluid'>
                    <div class='col-sm-12' id='price_err' style='color:red;'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='upload_price_file'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_trial_'>Close</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $user
     */
    function adjust_personal_trial_key($user)
    {
        $unix_start = strtotime($user->start);
        $unix_end = strtotime($user->end);
        $query = "update mdl_trial_keys "
            . "set start_date='$unix_start' , exp_date='$unix_end' "
            . "where userid=$user->userid and groupid=$user->groupid";
        echo "Query: " . $query . "<br>";
        $this->db->query($query);
    }

    /**
     * @param $users
     */
    function adjust_group_trial_keys($users)
    {
        $dataObj = json_decode($users);
        $users_data = (array)json_decode(json_decode($dataObj->users));
        foreach ($users_data as $userObj) {
            $unix_start = strtotime($dataObj->start);
            $unix_end = strtotime($dataObj->end);
            $query = "update mdl_trial_keys "
                . "set start_date='$unix_start', exp_date='$unix_end' "
                . "where userid=$userObj->userid "
                . "and groupid=$userObj->groupid";
            $this->db->query($query);
        } // end foreach
    }

    /**
     *
     */
    function logout()
    {
        session_destroy();
    }

    /**
     * @return string
     */
    function get_templates_list()
    {
        $list = "";

        $list .= "<select id='templates_list' style='width:365px;'>";
        $list .= "<option value='0' selected>Please select template</option>";
        $query = "select * from mdl_email_templates order by template_name";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $item = $row['template_name'];
            $list .= "<option value='$id'>$item</option>";
        }
        $list .= "</select>";

        return $list;
    }

    /**
     * @return string
     */
    function get_account_tab()
    {
        $list = "";
        $templates = $this->get_templates_list();
        $list .= "<div class='container-fluid'>";
        $list .= "<span class='col-sm-6'>$templates</span>";
        $list .= "</div>";

        $list .= "<div class='container-fluid'>";
        $list .= "<span class='col-sm-12' id='template_content'></span>";
        $list .= "</div><br><br>";

        $list .= "<div class='container-fluid'>";
        $list .= "<span class='col-sm-6'><button type='button' class='btn btn-default' id='logout_utils'>Logout</button></span>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $id
     *
     * @return string
     */
    function get_email_template($id)
    {
        $list = "";
        $query = "select * from mdl_email_templates where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $content = $row['template_content'];
        }

        $list .= "<div class='container-fluid'>";
        $list .= "<span class='col-sm-12'>";
        $list .= "<br><textarea name='editor1' id='editor1' rows='10' style='width:675px;'>$content</textarea>";
        $list
            .= "<script>
                CKEDITOR.replace( 'editor1' );
            </script>";
        $list .= "</span>";
        $list .= "</div><br>";
        $list .= "<input type='hidden' id='template_id' value='$id'>";
        $list .= "<div class='container-fluid'>";
        $list .= "<span class='col-sm-6'><button type='button' class='btn btn-default' id='update_template'>Update</button></span>";
        $list .= "</div><br>";

        return $list;
    }

    /**
     * @param $t
     */
    function update_email_template($t)
    {
        $query = "update mdl_email_templates "
            . "set template_content='$t->content' where id=$t->id";
        $this->db->query($query);
    }

    /**
     * @param $name
     *
     * @return int
     */
    function is_price_item_exists($name)
    {
        $query = "select * from mdl_price where institute='$name'";
        $num = $this->db->numrows($query);

        return $num;
    }

    /**
     *
     */
    function update_price_items()
    {
        $un = array();
        $query = "select * from mdl_user where deleted=0 "
            . "and institution<>'' and institution<>'n/a'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $un[] = mb_convert_encoding($row['institution'], 'UTF-8');
        }
        $clear_data = array_unique($un);
        foreach ($clear_data as $name) {
            $status = $this->is_price_item_exists($name);
            if ($status == 0) {
                $clearname = addslashes($name);
                $query
                    = "insert into mdl_price (institute) values ('$clearname')";
            } // end if
        } // end foreach
    }

    /**
     * @return string
     */
    function get_prices_page()
    {
        $list = "";
        $this->update_price_items();

        $list .= "<br><div class='padding-left:25px;'>";
        $list .= "<span class='col-sm-2'><button class='btn btn-default' id='add_new_school'>Add New School</button></span>";
        $list .= "<span class='col-sm-2'><button class='btn btn-default' id='get_price_upload_dialog'>Upload</button></span>";
        $list .= "</div>";

        $list .= "<br><br><table id='price_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Schoolname</th>";
        $list .= "<th>Price</th>";
        $list .= "<th>Operations</th>";
        $list .= "</tr>";
        $list .= "</thead>";
        $list .= "<tbody>";
        $query = "select * from mdl_price order by institute";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['institute'];
            $price = $row['price'];
            $link
                = "<a href='#' onClick='return false;' class='price_adjust' data-id='"
                . $row['id'] . "'>Adjust</a>";
            $list .= "<tr>";
            $list .= "<td>$name</td>";
            $list .= "<td>$$price</td>";
            $list .= "<td>$link</td>";
            $list .= "</tr>";
        }
        $list .= "</tbody>";
        $list .= "</table>";

        return $list;
    }

    /**
     * @param $item
     */
    function update_item_price($item)
    {
        $id = $item->id;
        $price = $item->price;
        $query = "update mdl_price set price='$price' where id=$id";
        $this->db->query($query);
    }

    /**
     * @param $item
     */
    function add_new_school_to_db($item)
    {
        $name = $item->name;
        $price = $item->price;
        $query = "insert into mdl_price "
            . "(institute,price) "
            . "values ('$name','$price')";
        $this->db->query($query);
    }

    /**
     * @return array
     */
    function get_archive_items()
    {
        $items = array();
        $query = "select * from mdl_article where active=1 order by title";
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

        return $items;
    }

    /**
     * @return string
     */
    function get_archive_page()
    {
        $list = "";
        $items = $this->get_archive_items();

        $list .= "<div class='row-fluid'>";
        $list .= "<br><br><table id='archive_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Title</th>";
        $list .= "<th>Link</th>";
        $list .= "<th>Date</th>";
        $list .= "<th>Operations</th>";
        $list .= "</tr>";
        $list .= "</thead>";

        $list .= "<tbody>";

        if (count($items) > 0) {
            foreach ($items as $item) {
                $date = $item->path;
                $link = "http://www." . $_SERVER['SERVER_NAME'] . "/articles/$item->path";
                $path = "<a href='$link' target='_blank'>$item->path</a>";
                $list .= "<tr>";
                $list .= "<td>$item->title</td>";
                $list .= "<td>$path</td>";
                $list .= "<td>$date</td>";
                $list .= "<td><a href='#' onclick='return false;' class='ar_item_del' data-id='$item->id'>Delete</a>";
                $list .= "&nbsp;&nbsp;&nbsp;<a href='#' onclick='return false;' class='ar_item_edit' data-id='$item->id'>Edit</a></td>";
                $list .= "</tr>";
            } // end foreach
        } // end if (count($items) > 0

        $list .= "</tbody>";
        $list .= "</table>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $id
     *
     * @return string
     */
    function get_edit_article_modal_dialog($id)
    {
        $list = "";

        $query = "select * from mdl_article where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $start = date('m/d/Y', $row['start']);
            $expire = date('m/d/Y', $row['expire']);
            $title = $row['title'];
        }

        $list
            .= " <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    <input type='hidden' id='aid' value='$id'>
                    <h4 class='modal-title'>Edit article dates</h4>
                  </div>
                  <div class='modal-body'>
            
                    <div class='container-fluid'>
                    <div class='col-sm-6' style='margin-left: 6%;'><input type='text' id='ae_title' value='$title' style='width: 100%;'></div>
                    </div>
                 
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Date1*</div>
                    <div class='col-sm-3'><input type='text' id='ae_date1' value='$start'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Date2*</div>
                    <div class='col-sm-3'><input type='text' id='ae_date2' value='$expire'></div>
                    </div>
                    
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-6' style='color: red;' id='ae_err'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='change_article_dates_done'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_article'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $item
     */
    function update_article_dates($item)
    {
        $upath = $this->get_article_directory($item->date1, $item->date2);
        $query = "select * from mdl_article where id=$item->aid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $opath = $row['path'];
        }
        $new_path = $_SERVER['DOCUMENT_ROOT'] . "/articles/$upath";
        $old_path = $_SERVER['DOCUMENT_ROOT'] . "/articles/$opath";
        rename($old_path, $new_path);
        $ustart = strtotime($item->date1);
        $expire = strtotime($item->date2);
        $clear_title = addslashes($item->title);
        $query
            = "update mdl_article 
                  set path='$upath', 
                  title='$clear_title',
                  start='$ustart', 
                  expire='$expire' where id=$item->aid";
        $this->db->query($query);
    }

    /**
     * @return string
     */
    function get_upload_archive_modal_dialog()
    {
        $list = "";

        $list
            .= " <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Upload PDF File</h4>
                  </div>
                  <div class='modal-body'>
                 
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Title*</div>
                    <div class='col-sm-3'><input type='text' id='title' ></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Date*</div>
                    <div class='col-sm-3'><input type='text' id='adate'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>File*</div>
                    <div class='col-sm-3'><input id='uploadBtn' type='file' class='upload' /></div>
                    </div>

                    <div class='container-fluid'>
                    <div class='col-sm-3'>&nbsp;</div>
                    <div class='col-sm-3' id='archive_err' style='color:red;'></div>
                    </div>
                    
                    <div class='container-fluid'>
                    <div class='col-sm-3'>&nbsp;</div>
                    <div class='col-sm-3' style='display:none;' id='loader'><img src='https://www.newsfactsandanalysis.com/assets/images/load.gif'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='upload_archive_ok'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $files
     * @param $data
     */
    function upload_archive_article($files, $data)
    {
        if ($files['error'] == 0 && $files['size'] > 0) {
            $date = strtotime($data['adate']);
            $now = time();
            $title = $data['title'];
            $destfile = "arcticle_$now.pdf";
            $dest = $_SERVER['DOCUMENT_ROOT'] . "/lms/utils/archive/$destfile";
            $status = move_uploaded_file($files['tmp_name'], $dest);
            if ($status) {
                $query = "insert into mdl_archive (title,path,adate) values ('$title','$destfile','$date')";
                $this->db->query($query);
            }
        } // end if
    }

    /**
     * @param $dir
     */
    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }


    /**
     * @param $id
     */
    function remove_article_directory($id)
    {
        $query = "select * from mdl_article where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $opath = $row['path'];
        }
        $oldpath = $_SERVER['DOCUMENT_ROOT'] . "/lms/articles/$opath";
        $newpath = $oldpath . '_deleted';
        rename($oldpath, $newpath);
    }

    /**
     * @param $id
     */
    function delete_archive_article($id)
    {
        $this->remove_article_directory($id);
        $query = "update mdl_article set active=0 where id=$id";
        $this->db->query($query);
    }

    /**
     * @param $files
     */
    function upload_price_csv_data($files)
    {
        if ($files['error'] == 0 && $files['size'] > 0) {
            $now = time();
            $destfile = "prices_$now.csv";
            $dest = $_SERVER['DOCUMENT_ROOT']
                . "/lms/utils/archive/$destfile";
            $status = move_uploaded_file($files['tmp_name'], $dest);
            if ($status) {
                $csv = array_map('str_getcsv', file($dest));
                /*
                echo "<pre>";
                print_r($csv);
                echo "</pre>";
                */


                if (count($csv) > 0) {
                    foreach ($csv as $item) {
                        $title = $item[0];
                        $price = $item[1];
                        if ($title != '' && $price != '') {
                            $exists = $this->is_price_item_exists($title);
                            if ($exists == 0) {
                                $query
                                    = "insert into mdl_price (institute,price) "
                                    . "values ('$title','$price')";
                                $this->db->query($query);
                            } // end if $exists==0
                        } // end if $title!='' && $price!=''
                    } // end foreach
                } // end if count($csv)>0


            } // end if $status
        } // end if files ...
    }

    /******************************************************************************************
     *
     *                              Publish article section
     *
     ******************************************************************************************/

    function get_publish_page()
    {
        $list = "";

        $list .= "<div class='row' style='margin-top: 25px;'>";

        $list .= "<table>";
        $list .= "<tr>";
        $list .= "<td style='padding: 15px;'><input type='file' id='files' multiple</td>";
        $list .= "<td style='padding: 15px;'><input type='text' id='title' placeholder='News title' style='width:200px;'></td>";
        $list .= "<td style='padding: 15px;'><input type='text' id='a_date1' placeholder='Date1'></td>";
        $list .= "<td style='padding: 15px;'><input type='text' id='a_date2' placeholder='Date2'></td>";
        $list .= "<td style='padding: 15px;'><button id='publish' class='btn btn-primary'>Publish</button></td>";
        $list .= "</tr>";
        $list .= "</table>";

        $list .= "</div>";

        $list .= "<div class='row' style='text-align: center;'>";
        $list .= "<span class='col-md-12' style='display: none;' id='ajax_loader'><img src='../../assets/images/ajax.gif'></span>";
        $list .= "</div>";

        $list .= "<div class='row'>";
        $list .= "<span class='col-md-12' style='color: red;' id='pub_err'></span>";
        $list .= "</div>";

        $list .= $this->get_archive_page();

        return $list;
    }


    /**
     * @param $date1
     * @param $date2
     *
     * @return string
     */
    function get_article_directory($date1, $date2)
    {
        $date1_arr = explode('/', $date1);
        $date2_arr = explode('/', $date2);
        $dir1 = $date1_arr[0] . '-' . $date1_arr[1] . '-' . $date1_arr[2];
        $dir2 = $date2_arr[0] . '-' . $date2_arr[1] . '-' . $date2_arr[2];
        $dir = $dir1 . '_' . $dir2;

        return $dir;
    }

    /**
     * @param $file
     *
     * @return bool|string
     */
    function unzip_archive($file)
    {
        $path = $file['tmp_name'];
        $now = time();
        $tmpdir = $_SERVER['DOCUMENT_ROOT'] . "/lms/tmp/$now";
        mkdir($tmpdir, 0777);
        $zip = new ZipArchive;
        if ($zip->open($path) === true) {
            $zip->extractTo($tmpdir);
            $zip->close();

            return $tmpdir;
        } // end if
        else {
            return false;
        }
    }


    /**
     * @param $tmpdir
     *
     * @return bool
     */
    function verify_archive($tmpdir)
    {
        $hasindex = 0;
        $imgdir = $tmpdir . '/assets/images';
        $dirstatus = is_dir($imgdir);
        $files = scandir($tmpdir);
        foreach ($files as $file) {
            if ($file == 'index.php') {
                $hasindex = 1;
            } // end if
        } // end foreach
        if ($hasindex && $dirstatus) {
            return true;
        } // end if
        else {
            return false;
        }
    }

    /**
     * @param $file
     * @param $post
     *
     * @return bool|string
     */
    function move_arcticle($file, $post)
    {
        $path = $file['tmp_name'];
        $date1 = $post['date1'];
        $date2 = $post['date2'];
        $adir = $this->get_article_directory($date1, $date2);
        //$dir = $_SERVER['DOCUMENT_ROOT'] . "/lms/articles/$adir";
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/articles/$adir";
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $zip = new ZipArchive;
        if ($zip->open($path) === true) {
            $zip->extractTo($dir);
            $zip->close();

            return $dir;
        } // end if
        else {
            return false;
        }
    }

    /**
     * @param $newsdir
     *
     * @return int
     */
    function is_news_exists($newsdir)
    {
        $query = "select * from mdl_article where path='$newsdir'";
        $num = $this->db->numrows($query);

        return $num;
    }

    /**
     * @param $post
     */
    function update_article_data($post)
    {
        $now = time();
        $newsdir = $this->get_article_directory($post['date1'],
            $post['date2']);
        $news_dir_status = $this->is_news_exists($newsdir);
        $start = strtotime($post['date1']);
        $expire = strtotime($post['date2']);
        if ($news_dir_status == 0) {
            $query
                = "insert into mdl_article (title,  path, start, expire, added)
					values ('" . $post['title'] . "','" . $newsdir
                . "', '$start', '$expire', '" . $now . "')";
        } // end if
        else {
            $query
                = "update mdl_article set added='$now' where path='$newsdir'";
        }
        $this->db->query($query);
        $this->create_json_data('article');
    }


    /**
     * @param $file
     * @param $post
     */
    function upload_article_file($file, $post)
    {
        if ($file['error'] == 0 && $file['size'] > 0) {
            $tmpdir = $this->unzip_archive($file);
            if ($tmpdir) {
                echo "1 ) File unzipped .... starting verification .... <br>";
                $vstatus = $this->verify_archive($tmpdir);
                if ($vstatus) {
                    echo "2 ) Verification passed .... starting article uploading .... <br>";
                    $move_status = $this->move_arcticle($file, $post);
                    if ($move_status) {
                        $this->update_article_data($post);
                        echo "3 ) Article was successfully uploaded <br>";
                    } // end if
                    else {
                        echo "Article was not uploaded <br>";
                    }
                } // end if
                else {
                    echo "2) Verification is not passed ";
                } // end else
            } // end if
            else {
                echo "File was not unzipped ...";
            }
        } // end if
        else {
            die ("I can't upload file to the server");
        }
    }

    /******************************************************************************************
     *
     *                              News quiz section
     *
     ******************************************************************************************/

    function get_news_quiz_page()
    {
        $list = "";
        $items = array();
        $query = "select * from mdl_poll order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = new stdClass();
                foreach ($row as $key => $value) {
                    $item->$key = $value;
                }
                $items[] = $item;
            }
        } // end if $num>0
        $list .= $this->create_quiz_table($items);

        return $list;
    }

    /**
     * @param $pid
     *
     * @return string
     */
    function get_quiz_questions($pid)
    {
        $list = "";

        $query = "select * from mdl_poll_q where pid=$pid";
        $result = $this->db->query($query);
        $i = 1;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $title = $row['title'];
            $list .= "<div class='row'>";
            $list .= "<span class='col-md-12'>$i) $title<br></span>";
            $list .= "</div>";
            $i++;
        }

        return $list;
    }

    /**
     * @param $items
     *
     * @return string
     */
    function create_quiz_table($items)
    {

        $list = "";

        $list .= "<div class='row' style='margin-top: 25px;'>";
        $list .= "<span class='col-lg-4'><button class='btn btn-default' id='add_poll'>Add Poll</button></span>";
        $list .= "<span class='col-lg-4'><button class='btn btn-default' id='add_quiz'>Add Quiz</button></span>";
        $list .= "</div>";

        $list .= "<div class='row' style='margin-top: 25px;'>";
        $list .= "<span class='col-md-12'>";
        $list .= "<table id='poll_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Title</th>";
        $list .= "<th>Artile</th>";
        $list .= "<th>Type</th>";
        $list .= "<th>Questions</th>";
        $list .= "<th>Date</th>";
        $list .= "</tr>";
        $list .= "</thead>";

        $list .= "<tbody>";
        if (count($items) > 0) {
            foreach ($items as $item) {
                $title = $item->title;
                $type = ($item->type == 1) ? 'Poll' : 'Quiz';
                $article = $this->get_article_title($item->aid);
                $date = date('m-d-Y', $item->added);
                $questions = $this->get_quiz_questions($item->id);
                $list .= "<tr>";
                $list .= "<td>$title</td>";
                $list .= "<td>$article</td>";
                $list .= "<td>$type</td>";
                $list .= "<td>$questions</td>";
                $list .= "<td>$date</td>";
                $list .= "</tr>";
            } // end foreach
        } // end if count($items)>0
        $list .= "</tbody>";

        $list .= "</table>";
        $list .= "</span>";
        $list .= "</div>";

        return $list;

    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_article_title($id)
    {
        $query = "select * from mdl_article where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $title = $row['title'];
        }

        return $title;
    }

    /**
     * @return string
     */
    function get_total_questions_dropbbox()
    {
        $list = "";

        $list .= "<select id='q_total'>";
        for ($i = 1; $i <= 75; $i++) {
            $list .= "<option value='$i'>$i</option>";
        }
        $list .= "</select>";

        return $list;
    }

    /**
     * @param $type
     *
     * @return string
     */
    function get_news_wizard($type)
    {
        $list = "";
        $title = ($type == 1) ? 'Poll params' : 'Quiz params';
        $totalbox = $this->get_total_questions_dropbbox();

        $list .= "<div class='panel panel-default' style='margin-top: 15px;'>
			  		<div class='panel-heading'>$title </div>
			  		<div class='panel-body'>
			  		<input type='hidden' id='type' value='$type'>
			  		
			  		<div class='row' style='padding: 15px;'>
			  		<span class='col-md-3'>Title*</span>
			  		<span class='col-md-3'><input type='text' id='qtitle' style='width: 375px;'></span>
			  		</div>
			  		
			  		<div class='row' style='padding: 15px;'>
			  		<span class='col-md-3'>Related article*</span>
			  		<span class='col-md-3'><input type='text' id='article' style='width: 375px;'></span>
			  		</div>
			  		
			  		<div class='row'>
			  		<span class='col-md-3' style='margin-left: 15px;'>Number of questions:</span>
			  		<span class='col-md-3'>$totalbox</span>
			  		</div>
			  		
			  		<div class='row' style='margin-top: 15px;'>
			  		<span class='col-md-6' style='margin-left: 15px;color: red;' id='qStep1Error'></span>
			  		</div>
			  		
			  		<div class='row' style='margin-top: 15px;'>
			  		<span class='col-md-3' style='margin-left: 15px;'><button class='btn btn-primary' id='qnextStep2'>Next</button></span>
			  		<span class='col-md-3'><button class='btn btn-primary' id='cancelQuiz'>Cancel</button></span>
			  		</div>
			  		
			  		</div>
				</div>";

        return $list;
    }

    /**
     * @param $id
     *
     * @return string
     */
    function get_question_answers($id, $type)
    {
        $list = "";
        for ($i = 1; $i <= 5; $i++) {
            $index = $id . '_' . $i;
            if ($type == 2) {
                $list .= "<div class='row' style='padding: 15px;'>";
                $list .= "<span class='col-md-2'>Answer$i</span>";
                $list .= "<span class='col-md-8'><input type='text' class='answers$id' style='width: 100%' data-id='$i'></span>";
                $list .= "<span class='col-md-2'><input type='checkbox' class='correct_answers$id' id='ca_$index' data-id='$i'>&nbsp; Correct Reply</span>";
                $list .= "</div>";
            } // end if
            else {
                $list .= "<div class='row' style='padding: 15px;'>";
                $list .= "<span class='col-md-2'>Answer$i</span>";
                $list .= "<span class='col-md-8'><input type='text' class='answers$id' style='width: 100%' data-id='$i'></span>";
                $list .= "<span class='col-md-2' style='display: none;'><input type='checkbox' checked class='correct_answers$id' id='ca_$index' data-id='$i'>&nbsp; Correct Reply</span>";
                $list .= "</div>";
            }
        }

        return $list;
    }


    /**
     * @param $total
     *
     * @return string
     */
    function get_questions_block($total, $type)
    {
        $list = "";
        for ($i = 1; $i <= $total; $i++) {
            $answers = $this->get_question_answers($i, $type);
            $list .= "<div class='row' style='padding: 15px;'>";
            $list .= "<span class='col-md-2'>Question#$i</span>";
            $list .= "<span class='col-md-10'><input type='text' class='questions' style='width: 100%' data-id='$i'></span>";
            $list .= "</div>";

            $list .= "<div class='row' style='padding: 15px;'>";
            $list .= "<span class='col-md-12'>$answers</span>";
            $list .= "</div>";

            $list .= "<div class='row' style='padding: 15px;'>";
            $list .= "<span class='col-md-12'><hr/></span>";
            $list .= "</div>";
        }

        $list .= "<div class='row' style='margin-top: 15px;'>";
        $list .= "<span class='col-md-12' id='quiz_err' style='color: red;'></span>";
        $list .= "</div>";

        $list .= "<div class='row' style='margin-top: 15px;'>";
        $list .= "<span class='col-md-2'><button class='btn btn-primary' id='add_new_quiz_item'>Submit</button></span>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $item
     *
     * @return string
     */
    function get_quiz_page_step2($item)
    {
        $list = "";
        $questions = $this->get_questions_block($item->total, $item->type);
        $list .= "<div class='panel panel-default' style='margin-top: 15px;'>
			  		<div class='panel-heading'>Questions</div>
			  		<div class='panel-body'>$questions";

        $list .= "</div>";
        $list .= "</div>";

        return $list;

    }

    /**
     * @param $item
     *
     * @return mixed
     */
    function get_article_id_by_title($item)
    {
        $data = explode('&&&', $item);
        $title = $data[0];
        $path = $data[1];
        $query
            = "select * from mdl_article where title='$title' and path='$path'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }

        return $id;
    }

    /**
     * @param $aid
     * @param $type
     *
     * @return int
     */
    function is_poll_exists($aid, $type)
    {
        $query = "select * from mdl_poll where aid=$aid and type=$type";
        $num = $this->db->numrows($query);

        return $num;
    }


    /**
     * @param $item
     *
     * @return string
     */
    function add_new_quiz($item)
    {
        $list = "";
        $response = ($item->type == 1) ? 'poll' : 'quiz';

        $now = time();
        $aid = $this->get_article_id_by_title($item->article);

        $status = $this->is_poll_exists($aid, $item->type);

        if ($status == 0) {
            $query
                = "insert into mdl_poll (aid, type, title, added) values ($aid, $item->type, '"
                . addslashes($item->title) . "', '$now') ";
            $this->db->query($query);
            $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
            $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
            $pollID = $lastid_arr[0];

            $questions = $item->questions;
            foreach ($questions as $q) {
                $query
                    = "insert into mdl_poll_q (pid, title, added) values ($pollID, '"
                    . addslashes($q->text) . "', '$now')";
                $this->db->query($query);
                $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
                $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
                $questionID = $lastid_arr[0];

                $answers = $q->a;
                foreach ($answers as $a) {
                    $ca = ($a->ca->status == 'Yes') ? '1' : '0';
                    $query
                        = "insert into mdl_poll_a (qid, a, correct) values ($questionID, '"
                        . addslashes($a->text) . "', $ca)";
                    //echo "Query: " . $query . "<br>";
                    $this->db->query($query);
                } // end foreach
            } // end foreach

            $list .= "<div class='row' style='margin-top: 15px;'>";
            $list .= "<span class='col-md-4'>New $response was successfully added</span>";
            $list .= "<span class='col-md-3'><button class='btn btn-primary' id='cancelQuiz'>Back to quizzes</button></span>";
            $list .= "</div>";
        } // end if
        else {
            $list .= "<div class='row' style='margin-top: 15px;'>";
            $list .= "<span class='col-md-4'>This item already exists</span>";
            $list .= "<span class='col-md-3'><button class='btn btn-primary' id='cancelQuiz'>Back to quizzes</button></span>";
            $list .= "</div>";
        } // end else

        return $list;
    }


    /******************************************************************************************
     *
     *                              News forum section
     *
     ******************************************************************************************/

    function get_news_forum_page()
    {
        $list = "";
        $items = array();
        $query = "select * from mdl_board order by title";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = new stdClass();
                foreach ($row as $key => $value) {
                    $item->$key = $value;
                }
                $items[] = $item;
            }
        }
        $list .= $this->create_forum_page($items);

        return $list;
    }


    /**
     * @return string
     */
    function get_post_treshold_dropbox()
    {
        $list = "";

        $list .= "<select id='post_threshold'>";

        $query = "select * from mdl_edit_post_treshold";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $treshold = $row['threshold'];
        }

        for ($i = 1; $i <= 10; $i++) {
            if ($i == $treshold) {
                $list .= "<option value='$i' selected>$i</option>";
            } // end if
            else {
                $list .= "<option value='$i'>$i</option>";
            } // end esle
        }

        $list .= "</select>";


        return $list;
    }


    /**
     * @return string
     */
    function get_edit_thresold_block()
    {
        $list = "";

        $list .= "<br><div class='row' style='text-align: left;'>";
        $box = $this->get_post_treshold_dropbox();
        $list .= "<span class='col-md-4' style='font-weight: bold;'>Discussion board edit post threshold:</span>";
        $list .= "<span class='col-md-2'>$box &nbsp;h</span>";
        $list .= "<span class='col-md-2'><button id='update_post_treshold'>Update</button></span>";
        $list .= "</div>";

        return $list;
    }

    function update_post_treshold($period)
    {
        $query = "update mdl_edit_post_treshold set threshold='$period'";
        $this->db->query($query);
    }

    /**
     * @param $items
     *
     * @return string
     */
    function create_forum_page($items)
    {
        $list = "";


        $list .= "<div class='row' style='margin-top: 25px;'>";
        $list .= "<span class='col-lg-4'><button class='btn btn-default' id='add_forum'>Add Board</button></span>";
        $list .= "</div>";

        $list .= $this->get_edit_thresold_block();

        $list .= "<div class='row' style='margin-top: 25px;'>";
        $list .= "<span class='col-md-12'>";
        $list .= "<table id='forum_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Title</th>";
        $list .= "<th>Article</th>";
        $list .= "<th>Added</th>";
        $list .= "</tr>";
        $list .= "</thead>";

        $list .= "<tbody>";
        if (count($items) > 0) {
            foreach ($items as $item) {
                $article = $this->get_article_name_by_id($item->aid);
                $title = $item->title;
                $date = date('m-d-Y', $item->added);
                $list .= "<tr>";
                $list .= "<td>$title</td>";
                $list .= "<td>$article</td>";
                $list .= "<td>$date</td>";
                $list .= "</tr>";
            } // end foreach
        } // end if count count( $items) > 0
        $list .= "</tbody>";

        $list .= "</table>";
        $list .= "</span>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_article_name_by_id($id)
    {
        $query = "select * from mdl_article where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $title = $row['title'];
        }

        return $title;
    }

    /**
     * @return string
     */
    function get_add_forum_page()
    {
        $list = "";

        $list
            .= "<div class='panel panel-default' style='margin-top: 15px;'>
			  		<div class='panel-heading'>Add New Discussion Board </div>
			  		<div class='panel-body'>
			  		
			  		<div class='row' style='padding: 15px;'>
			  		<span class='col-md-3'>Title*</span>
			  		<span class='col-md-3'><input type='text' id='ftitle' style='width: 375px;'></span>
			  		</div>
			  		
			  		<div class='row' style='padding: 15px;'>
			  		<span class='col-md-3'>Related article*</span>
			  		<span class='col-md-3'><input type='text' id='article' style='width: 375px;'></span>
			  		</div>
			  		
			  		<div class='row' style='margin-top: 15px;'>
			  		<span class='col-md-6' style='margin-left: 15px;color: red;' id='forum_err'></span>
			  		</div>
			  		
			  		<div class='row' style='margin-top: 15px;'>
			  		<span class='col-md-3' style='margin-left: 15px;'><button class='btn btn-primary' id='add_forum_done'>Submit</button></span>
			  		<span class='col-md-3'><button class='btn btn-primary' id='cancelForum'>Cancel</button></span>
			  		</div>
			  		
			  		</div>
				</div>";

        return $list;
    }

    /**
     * @param $aid
     *
     * @return int
     */
    function is_forum_exists($aid)
    {
        $query = "select * from mdl_board where aid=$aid";
        $num = $this->db->numrows($query);

        return $num;
    }

    /**
     * @param $item
     *
     * @return string
     */
    function add_new_forum($item)
    {
        $list = "";
        $now = time();
        $aid = $this->get_article_id_by_title($item->article);
        $status = $this->is_forum_exists($aid);
        if ($status == 0) {
            $query
                = "insert into mdl_board (aid, title, added) values ($aid, '$item->title','$now')";
            $this->db->query($query);

            $list .= "<div class='row' style='margin-top: 15px;'>";
            $list .= "<span class='col-md-5'>New Disscussion Board was successfully added.</span>";
            $list .= "<span class='col-md-3'><button class='btn btn-primary' id='cancelForum'>Return</button></span>";
            $list .= "</div>";
        } // end if
        else {
            $list .= "<div class='row' style='margin-top: 15px;'>";
            $list .= "<span class='col-md-5'>Item already exists</span>";
            $list .= "<span class='col-md-3'><button class='btn btn-primary' id='cancelForum'>Return</button></span>";
            $list .= "</div>";
        } // end else


        return $list;
    }

    /**
     * @return string
     */
    function get_online_classes_page()
    {
        $list = "";
        $items = array();
        $query = "select * from mdl_classes where active=1 order by date desc";
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
        } // end if $num>0
        $list .= $this->create_online_classes_page($items);

        return $list;
    }

    /**
     * @param $items
     *
     * @return string
     */
    function create_online_classes_page($items)
    {
        $list = "";
        $list .= "<br><br><div class='row'>";
        $list .= "<span class='col-md-2'><input type='text' id='oclass_title' placeholder='Online Class Name*'></span>";
        $list .= "<span class='col-md-2'><input type='text' id='oclass_classes' placeholder='Students Class*'></span>";
        $list .= "<span class='col-md-2'><input type='text' id='oclass_date' placeholder='Class Date*'></span>";
        $list .= "<span class='col-md-2'><button class='btn btn-primary' id='add_new_video_chat'>Add</button></span>";
        $list .= "</div>";

        $list .= "<div class='row'>";
        $list .= "<span class='col-md-12' style='color:red;' id='oclass_err'></span>";
        $list .= "</div>";

        $list .= "<br><br><table id='online_classes_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Onlince class</th>";
        $list .= "<th>Class</th>";
        $list .= "<th>Date</th>";
        $list .= "<th>Ops</th>";
        $list .= "</tr>";
        $list .= "</thead>";

        $list .= "<tbody>";

        if (count($items) > 0) {
            foreach ($items as $item) {
                $date = date('m-d-Y h:i:s', $item->date);
                $groupname = $this->get_group_name($item->groupid);
                $ops = $this->get_online_classes_ops($item->id);
                $list .= "<tr>";
                $list .= "<td>$item->title</td>";
                $list .= "<td>$groupname</td>";
                $list .= "<td>$date</td>";
                $list .= "<td>$ops</td>";
                $list .= "</tr>";
            } // end foreach
        } // end count($items)

        $list .= "</tbody>";

        $list .= "</table>";

        return $list;
    }

    /**
     * @param $item
     */
    function add_new_online_class($item)
    {
        $date = strtotime($item->cdate);
        $groupid = $this->get_group_id($item->group);
        $query
            = "insert into mdl_classes 
                (title, groupid, date) 
                values ('$item->title','$groupid','$date')";
        $this->db->query($query);
    }

    /**
     * @param $id
     */
    function delete_online_class($id)
    {
        $query = "update mdl_classes set active=0 where id=$id";
        $this->db->query($query);
    }

    /**
     * @param $id
     *
     * @return string
     */
    function get_online_classes_ops($id)
    {
        $list = "";
        $list .= "<div class='row'>";
        $list .= "<span class='col-md-12'><button class='btn btn-default' id='del_online_class_$id'>Delete</button></span>";
        $list .= "</div>";

        return $list;
    }


}
