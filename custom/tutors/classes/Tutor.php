<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Tutor extends Utils
{

    /**
     * Tutor constructor.
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $groupname
     * @return int
     */
    function create_group($groupname)
    {
        if ($groupname != '') {
            $status = $this->is_group_exists($groupname);
            if ($status == 0) {
                $query = "insert into mdl_groups "
                    . "(courseid,idnumber,name) "
                    . "values($this->courseid,"
                    . " ' ',"
                    . " '" . $groupname . "')";
                $this->db->query($query);
                $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
                $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
                $lastId = $lastid_arr[0];
            } // end if $status==0
            else {
                $lastId = 0;
            }
        } // end if $groupname!=''
        else {
            $lastId = 0;
        }
        return $lastId;
    }

    /**
     * @param $user
     */
    function confirm_tutor($user)
    {
        $query = "update mdl_user set policyagreed='1' where email='$user->email'";
        $this->db->query($query);
    }

    /**
     * @param $user
     * @return string
     */
    function tutor_signup($user)
    {
        $list = "";
        $groups = array();
        $result = $this->signup($user);
        if ($result !== false) {
            $roleid = 4; // non-editing teacher
            $userObj = json_decode($user);
            $email = $userObj->email;
            $userid = $this->get_user_id($email);
            $userObj->userid = $userid;
            $this->enrol_user($userid, $roleid);

            $course1 = $userObj->course1;
            $groupid = $this->create_group($course1);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course2 = $userObj->course2;
            $groupid = $this->create_group($course2);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course3 = $userObj->course3;
            $groupid = $this->create_group($course3);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course4 = $userObj->course4;
            $groupid = $this->create_group($course4);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course5 = $userObj->course5;
            $groupid = $this->create_group($course5);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $course6 = $userObj->course6;
            $groupid = $this->create_group($course6);
            if ($groupid > 0) {
                $this->add_to_group($groupid, $userid);
                $groups[] = $groupid;
            }

            $this->confirm_tutor($userObj);
            $userObj->confirmed = 1;
            $userObj->confirmed = 1;
            $userObj->groups = $groups;
            $this->send_tutor_confirmation_email($userObj);
            $list .= "Thank you for signup. Confirmation email is sent to $userObj->email .";
        } // end if $result!==false
        else {
            $list .= "Signup error happened";
        } // end else 
        return $list;
    }

    /**
     * @param $user
     * @param bool $output
     * @return bool|string
     */
    function verify_tutor($user, $output = TRUE)
    {
        $list = "";
        $page = file_get_contents($user->url);
        $status1 = strstr($page, $user->email);
        $status2 = strstr($page, $user->username);
        if ($status1 !== FALSE && $status2 !== FALSE) {
            $query = "update mdl_user set policyagreed='1' where email='$user->email'";
            $this->db->query($query);
            if ($output) {
                $list .= "Thank you. Your membership is confirmed";
            } // end if
            else {
                return TRUE;
            }
        } // end if
        else {
            if ($output) {
                $list .= "Your membership was not confirmed";
            } // end if 
            else {
                return FALSE;
            } // end else
        } // end else
        return $list;
    }

    /**
     * @param $user
     */
    function send_non_confirmed_tutor_notification($user)
    {
        $msg = "";
        $msg .= "<html>";
        $msg .= "<body>";

        $msg .= "<p>Non-confirmed professor's registration:</p>";

        $msg .= "<table>";

        $msg .= "<tr>";
        $msg .= "<td style='padding:15px;'>First name</td><td style='padding:15px;'>$user->firstname</td>";
        $msg .= "</tr>";

        $msg .= "<tr>";
        $msg .= "<td style='padding:15px;'>Last name</td><td style='padding:15px;'>$user->lastname</td>";
        $msg .= "</tr>";

        $msg .= "<tr>";
        $msg .= "<td style='padding:15px;'>Email</td><td style='padding:15px;'>$user->email</td>";
        $msg .= "</tr>";

        $msg .= "<tr>";
        $msg .= "<td style='padding:15px;'>Phone</td><td style='padding:15px;'>$user->phone</td>";
        $msg .= "</tr>";

        $msg .= "</table>";
        $msg .= "</body>";
        $msg .= "</html>";

        $subject = "Non-confirmed professor's registration";
        $recipientA = 'sirromas@gmail.com';
        $recipientB = 'steve@posnermail.com ';
        $this->send_email($subject, $msg, $recipientA);
        $this->send_email($subject, $msg, $recipientB);
    }

    /**
     * @param $user
     * @return string
     */
    function get_tutor_classes_signup_links($user)
    {
        $list = "";
        $groups = $user->groups;
        if (count($groups) > 0) {
            foreach ($groups as $id) {
                $name = $this->get_group_name($id);
                $list .= "<p><a href='http://www." . $_SERVER['SERVER_NAME'] . "/registerstudentbody.html?groupid=$id' target='_blank'>$name</a></p>";
            } // end foreach
        } // end if count($groups)>0

        return $list;
    }

    /**
     * @param $user
     * @return string
     */
    function get_tutor_classes($user)
    {
        $list = "";
        $groups = $user->groups;
        if (count($groups) > 0) {
            foreach ($groups as $id) {
                $name = $this->get_group_name($id);
                $list .= "<p>$name</p>";
            } // end foreach
        } // end if count($groups)>0
        return $list;
    }

    /**
     * @param $user
     * @return mixed
     */
    function get_tutor_confirmation_message($user)
    {
        if ($user->confirmed == 0) {
            $query = "select * from mdl_email_templates "
                . "where template_name='tutor_non_confirmed'";
        } // end if 
        else {
            $query = "select * from mdl_email_templates "
                . "where template_name='tutor_confirmed'";
        } // end else

        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $content = $row['template_content'];
        }

        $classes = $this->get_tutor_classes($user);
        $links = $this->get_tutor_classes_signup_links($user);
        $search = array('{firstname}', '{lastname}', '{email}', '{password}', '{class}', '{links}');
        $replace = array($user->firstname, $user->lastname, $user->email, $user->pwd, $classes, $links);
        $message = str_replace($search, $replace, $content);
        return $message;
    }

    /**
     * @param $user
     * @return bool
     */
    function send_tutor_confirmation_email($user)
    {
        $subject = 'Signup confirmation';
        $msg = "";
        $msg .= $this->get_tutor_confirmation_message($user);
        $result = $this->send_email($subject, $msg, $user->email);
        return $result;
    }

    /**
     * @return array
     */
    function get_archive_items()
    {
        $items = array();
        $query = "select * from mdl_archive order by adate desc";
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
        if (count($items) > 0) {
            $list .= "<div class='row-fluid'>";
            $list .= "<br><br><table id='archive_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th align='left'>Title</th>";
            $list .= "<th align='left'>Link</th>";
            $list .= "<th align='left'>Date</th>";
            //$list .= "<th align='left'>Operations</th>";
            $list .= "</tr>";
            $list .= "</thead>";
            $list .= "<tbody>";
            foreach ($items as $item) {
                $date = date('m-d-Y', $item->adate);
                $link = "https://" . $_SERVER['SERVER_NAME'] . "/lms/utils/archive/$item->path";
                $path = "<a href='$link' target='_blank'>$item->path</a>";
                $list .= "<tr>";
                $list .= "<td>$item->title</td>";
                $list .= "<td>$path</td>";
                $list .= "<td>$date</td>";
                //$list .= "<td><a href='#' onclick='return false;' class='ar_item_del' data-id='$item->id'>Delete</a></td>";
                $list .= "</tr>";
            } // end foreach
            $list .= "</tbody>";
            $list .= "</table>";
            $list .= "</div>";
        } // end if (count($items) > 0
        else {
            $list .= "<div class='row-fluid' style='padding-top:10px;'>";
            $list .= "<p style='text-align: center;'>There are no any archive pdf files uploaded</p>";
            $list .= "</div>";
        } // end else
        return $list;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get_employee_id_by_name($name)
    {
        $names = explode(' ', $name);
        if (count($names) == 2) {
            $query = "select * from tblstaff 
                    where firstname='" . $names[0] . "' 
                    and lastname='" . $names[1] . "'";
        } // end if
        else {
            $query = "select * from tblstaff 
                    where firstname='" . $names[0] . "'";
        } // end else
        $result = $this->db->query($query);
        foreach ($result->result() as $row) {
            $id = $row->id;
        }
        return $id;
    }

    /*****************************************************************************************************************
     *
     *                                       Grades page code
     *
     *****************************************************************************************************************/

    public function get_group_name_by_id($groupid)
    {
        $query = "select * from mdl_groups where id=$groupid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }
        return $name;
    }

    /**
     * @param $userid
     * @return array
     */
    public function get_tutors_groups_list($userid)
    {
        $groups = array();
        $query = "select * from mdl_groups_members where userid=$userid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $groups[] = $row['groupid'];
            }
        }
        return $groups;
    }

    /**
     * @param $userid
     * @return bool
     */
    function is_user_student($userid)
    {
        $contextid = $this->get_course_context();
        $query = "select * from mdl_role_assignments 
                  WHERE contextid=$contextid and userid=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $roleid = $row['roleid'];
        }
        $status = ($roleid == 5) ? true : false;
        return $status;
    }

    /**
     * @param $groupid
     * @return array
     */
    public function get_group_students($groupid)
    {
        $students = array();
        $query = "select * from mdl_groups_members where groupid=$groupid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $userid = $row['userid'];
                $status = $this->is_user_student($userid);
                if ($status) {
                    $students[] = $userid;
                } // end if status
            } // end while
        } // end if $num > 0
        return $students;
    }

    /**
     * @param $userid
     * @return array
     */
    public function get_tutor_students_list($userid)
    {
        $students = array();
        $groups = $this->get_tutors_groups_list($userid);
        if (count($groups) > 0) {
            foreach ($groups as $groupid) {
                $group_students = $this->get_group_students($groupid);
                if (count($group_students) > 0) {
                    foreach ($group_students as $userid) {
                        $students[] = $userid;
                    } // end foreach
                } // end if count($group_students)>0
            } // end foreach
        } // end if count($groups)>0
        return $students;
    }


    /**
     * @param null $users
     * @return array
     */
    public function get_quiz_qrade_items($users = null)
    {
        $ids = array();
        if ($users == null) {
            $query = "select * from mdl_grade_items where itemmodule='quiz'";
        } // end if
        else {
            $query = "select g.id, 
                      g.itemname, 
                      g.itemtype, 
                      g.itemmodule, 
                      g.iteminstance, 
                      gr.userid, 
                      gr.finalgrade, 
                      gr.timemodified from mdl_grade_items g, mdl_grade_grades gr 
                      where  g.id=gr.itemid 
                      and g.itemtype='mod' 
                      and g.itemmodule='quiz' 
                      and gr.userid in ($users)";
        }
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $ids[] = $row['id'];
        }
        return $ids;
    }


    /**
     * @param null $users
     * @return array
     */
    public function get_forum_grade_items($users = null)
    {
        $ids = array();
        if ($users == null) {
            $query = "select * from mdl_grade_items where itemmodule='forum'";
        } // end if
        else {
            $query = "select g.id, 
                      g.itemname, 
                      g.itemtype, 
                      g.itemmodule, 
                      g.iteminstance, 
                      gr.userid, 
                      gr.finalgrade, 
                      gr.timemodified from mdl_grade_items g, mdl_grade_grades gr 
                      where  g.id=gr.itemid 
                      and g.itemtype='mod' 
                      and g.itemmodule='forum' 
                      and gr.userid in ($users)";
        } // end else
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $ids[] = $row['id'];
        }
        return $ids;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get_item_name($id)
    {
        $query = "select * from mdl_grade_items where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['itemname'];
        }
        return $name;
    }

    /**
     * @param $itemid
     * @param $userid
     * @return string
     */
    public function get_student_grade_item_grades($itemid, $userid)
    {
        $query = "select * from mdl_grade_grades 
                where itemid=$itemid and userid=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $g = $row['finalgrade'];
        }
        $grade = ($g == null) ? '-' : $g;
        return $grade;
    }

    /**
     * @param $itemid
     * @param $userid
     * @return false|string
     */
    public function get_student_grades_date($itemid, $userid)
    {
        $query = "select * from mdl_grade_grades 
                where itemid=$itemid and userid=$userid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $date = date('m-d-Y', $row['timemodified']);
            }
        } // end if
        else {
            $date = 'N/A';
        }
        return $date;
    }

    /**
     * @param $userid
     * @return array
     */
    public function get_student_quiz_scores($userid)
    {
        $list = "";
        $total = 0;
        $items = $this->get_quiz_qrade_items();
        if (count($items) > 0) {
            foreach ($items as $itemid) {
                $item_name = $this->get_item_name($itemid);
                $item_grade = round($this->get_student_grade_item_grades($itemid, $userid));
                $item_date = $this->get_student_grades_date($itemid, $userid);
                $total = $total + $item_grade;

                $list .= "<div class='row-fluid'>";
                $list .= "<span>Quiz:</span>";
                $list .= "<span>$item_name</span>";
                $list .= "</div>";

                $list .= "<div class='row-fluid'>";
                $list .= "<span>Grade: </span>";
                $list .= "<span>$item_grade</span>";
                $list .= "</div>";

                if ($item_grade > 0) {
                    $list .= "<div class='row-fluid'>";
                    $list .= "<span>Date: </span>";
                    $list .= "<span><br>$item_date</span>";
                    $list .= "</div>";
                }

            } // end foreach
        } // end if count($items)>0
        $scores = array('list' => $list, 'total' => $total);
        return $scores;
    }

    /**
     * @param $userid
     * @return array
     */
    public function get_student_forum_scores($userid)
    {
        $list = "";
        $total = 0;
        $items = $this->get_forum_grade_items();
        if (count($items) > 0) {
            foreach ($items as $itemid) {
                $item_name = $this->get_item_name($itemid);
                $item_grade = round($this->get_student_grade_item_grades($itemid, $userid));
                $item_date = $this->get_student_grades_date($itemid, $userid);
                $total = $total + $item_grade;

                $list .= "<div class='row-fluid'>";
                $list .= "<span>Forum: </span>";
                $list .= "<span>$item_name</span>";
                $list .= "</div>";

                $list .= "<div class='row-fluid'>";
                $list .= "<span>Grade: </span>";
                $list .= "<span>$item_grade</span>";
                $list .= "</div>";

                if ($item_grade > 0) {
                    $list .= "<div class='row-fluid'>";
                    $list .= "<span>Date: </span>";
                    $list .= "<span><br>$item_date</span>";
                    $list .= "</div>";
                }

            } // end foreach
        } // end if count($items)>0
        $scores = array('list' => $list, 'total' => $total);
        return $scores;
    }

    /**
     * @param $userid
     * @return mixed
     */
    public function get_student_class($userid)
    {
        $query = "select * from mdl_groups_members where userid=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $groupid = $row['groupid'];
        }
        $groupname = $this->get_group_name($groupid);
        return $groupname;
    }

    /**
     * @param $userid
     * @return string
     */
    public function get_grades_page($userid)
    {
        $list = "";
        $list .= "<div class='row-fluid'>";
        $list .= "<br><br><table id='grades_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Student</th>";
        $list .= "<th>Email Address</th>";
        $list .= "<th>Class</th>";
        $list .= "<th>Quiz</th>";
        $list .= "<th>Forum</th>";
        $list .= "<th>Course Total</th>";
        $list .= "</tr>";
        $list .= "</thead>";
        $students = $this->get_tutor_students_list($userid);
        $list .= "<tbody>";
        if (count($students) > 0) {
            foreach ($students as $studentid) {
                $userdata = $this->get_user_details($studentid);
                $fname = $userdata->firstname;
                $lname = $userdata->lastname;
                $email = $userdata->email;
                $groupname = $this->get_student_class($studentid);
                $qscore = $this->get_student_quiz_scores($studentid);
                $fscore = $this->get_student_forum_scores($studentid);
                $quizScore = $qscore['list'];
                $forumScore = $fscore['list'];
                $courseTotal = $qscore['total'] + $fscore['total'];
                $list .= "<tr>";
                $list .= "<td>$fname $lname</td>";
                $list .= "<td>$email</td>";
                $list .= "<td>$groupname</td>";
                $list .= "<td>$quizScore</td>";
                $list .= "<td>$forumScore</td>";
                $list .= "<td>$courseTotal</td>";
                $list .= "</tr>";
            }  // end foreach
        } // end if count($students)>0
        $list .= "</tbody>";
        $list .= "</table>";
        $list .= "</div>";
        return $list;
    }


    /*****************************************************************************************************************
     *
     *                                       Export page code
     *
     *****************************************************************************************************************/

    public function get_tutor_groups_dropdown($groups)
    {
        $list = "";
        $list .= "<select id='tutor_groups' style='width: 175px;'>";
        $list .= "<option value='0' selected>Please select class</option>";
        foreach ($groups as $groupid) {
            $groupname = $this->get_group_name($groupid);
            $list .= "<option value='$groupid'>$groupname</option>";
        }
        $list .= "</select>";
        return $list;
    }


    /**
     * @param $userid
     * @return string
     */
    public function get_export_page($userid)
    {
        $list = "";
        $groups = $this->get_tutors_groups_list($userid);
        $groups_dropdown = $this->get_tutor_groups_dropdown($groups);

        $list .= "<br><br><div class='row-fluid' style='text-align: center;margin-left: 10%;'>";
        $list .= "<span class='col-md-3'>$groups_dropdown</span>";
        $list .= "<span class='col-md-2'><input type='checkbox' class='items' value='quiz' id='quiz_grades'>&nbsp; Quiz</span>";
        $list .= "<span class='col-md-2'><input type='checkbox' class='items' value='forum' id='forum_grades'>&nbsp; Forum</span>";
        // $list .= "<span class='col-md-2'><input type='checkbox' class='items' value='total' id='total_grades'>&nbsp; Total</span>";
        $list .= "<span class='col-md-2'><button id='make_export'>Go</button></span>";
        $list .= "</div>";

        $list .= "<br><br><div class='row-fluid' style='text-align: center;'>";
        $list .= "<span class='col-md-12' style='color: red;' id='export_err'></span>";
        $list .= "</div>";

        $list .= "<br><br><div class='row-fluid' style='text-align: center;' id='export_links'></div>";
        return $list;
    }


    /**
     * @param $studentid
     * @return array
     */
    public function get_student_quiz_grades($studentid)
    {
        $grades = array();
        $quiz_items = implode(',', $this->get_quiz_qrade_items());
        $query = "select * from mdl_grade_grades 
                where userid=$studentid 
                and itemid in ($quiz_items) and finalgrade is not null";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $grade = new stdClass();
                foreach ($row as $key => $value) {
                    $grade->$key = $value;
                } // end foreach
                $grades[] = $grade;
            } // end while
        } // end if $num > 0
        return $grades;
    }

    /**
     * @param $studentid
     * @return array
     */
    public function get_student_forum_grades($studentid)
    {
        $grades = array();
        $forum_items = implode(',', $this->get_forum_grade_items());
        $query = "select * from mdl_grade_grades 
                where userid=$studentid 
                and itemid in ($forum_items) and finalgrade is not null";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $grade = new stdClass();
                foreach ($row as $key => $value) {
                    $grade->$key = $value;
                } // end foreach
                $grades[] = $grade;
            } // end while
        } // end if $num > 0
        return $grades;
    }


    /**
     * @param $item
     * @return string
     */
    public function create_export_data($item)
    {
        $list = "";
        $groupid = $item->groupid;
        $items = explode(',', $item->items); // array of items to be exported
        $students = $this->get_group_students($groupid);

        $qpath = $_SERVER['DOCUMENT_ROOT'] . "/lms/custom/tutors/quiz_$groupid.csv";
        $output1 = fopen($qpath, 'w');

        $fpath = $_SERVER['DOCUMENT_ROOT'] . "/lms/custom/tutors/forum_$groupid.csv";
        $output2 = fopen($fpath, 'w');

        foreach ($students as $studentid) {
            $userdata = $this->get_user_details($studentid);
            foreach ($items as $itemid) {
                switch ($itemid) {
                    case 'quiz':
                        $gradeitems = $this->get_student_quiz_grades($studentid);
                        if (count($gradeitems) > 0) {
                            foreach ($gradeitems as $gr_item) {
                                $itemname = $this->get_item_name($gr_item->itemid);
                                $itemgrade = $this->get_student_grade_item_grades($gr_item->itemid, $studentid);
                                $itemdate = $this->get_student_grades_date($gr_item->itemid, $studentid);
                                $data = array($userdata->firstname, $userdata->lastname, $itemname, $itemgrade, $itemdate);
                                fputcsv($output1, $data);
                            } // end froeach
                        } // end if count($gradeitems)>0
                        break;
                    case 'forum':
                        $gradeitems = $this->get_student_forum_grades($studentid);
                        if (count($gradeitems) > 0) {
                            foreach ($gradeitems as $gr_item) {
                                $itemname = $this->get_item_name($gr_item->itemid);
                                $itemgrade = $this->get_student_grade_item_grades($gr_item->itemid, $studentid);
                                $itemdate = $this->get_student_grades_date($gr_item->itemid, $studentid);
                                $data = array($userdata->firstname, $userdata->lastname, $itemname, $itemgrade, $itemdate);
                                fputcsv($output2, $data);
                            } // end foreach
                        } // end if count($gradeitems)>0
                        break;
                } // end of switch
            } // end foreach items foreach to be exported
        } // end foreach for students
        fclose($output1);
        fclose($output2);
        $http_q = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/custom/tutors/quiz_$groupid.csv";
        $http_f = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/custom/tutors/forum_$groupid.csv";
        $list .= "<div class='row-fluid'>";
        $list .= "<span class='col-md-6'><a href='$http_q' target='_blank'>QUIZ GRADES</a></span><span class='col-md-6'><a href='$http_f' target='_blank'>DISCUSSION GRADES</a></span>";
        $list .= "</div>";
        return $list;
    }
}