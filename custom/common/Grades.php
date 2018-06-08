<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/authorize/Payment.php';

class Grades extends Utils
{

    /**
     * @var array
     */
    public $users = array();
    /**
     * @var array
     */
    public $news_poll_questions = array();
    /**
     * @var array
     */
    public $news_quiz_questions = array();
    /**
     * @var array
     */
    public $student_poll_score = array();
    /**
     * @var array
     */
    public $student_quiz_score = array();
    /**
     * @var array
     */
    public $student_forum_score = array();
    /**
     * @var
     */
    public $articleID;
    /**
     * @var int
     */
    public $courseid;


    /**
     * Grades constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->courseid = 2;
    }

    /**
     * @param $group_users
     */
    function merge_group_users($group_users)
    {
        foreach ($group_users as $userid) {
            $this->users[] = $userid;
        }
    }

    /**
     * @param $userid
     *
     * @return string
     */
    function get_grades_page($userid)
    {
        $list = "";
        $list .= $this->get_grades_pageV2($userid);

        return $list;

    }

    /**
     * @param $qid
     * @return mixed
     */
    function get_question_name($qid)
    {
        $query = "select * from mdl_poll_q where id=$qid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['title'];
        }

        return $name;
    }

    /**
     * @param $id
     * @return mixed
     */
    function get_answer_title($id)
    {
        $query = "select * from mdl_poll_a where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $a = $row['a'];
        }

        return $a;
    }

    /**
     * @param $qid
     * @param $ans
     * @param $userid
     * @return mixed
     */
    function get_student_reply($qid, $ans, $userid)
    {
        $ans_list = implode(',', $ans);
        $query = "select * from mdl_poll_student_answers 
                where userid=$userid and aid in ($ans_list)";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $answerid = $row['aid'];
        }

        return $answerid;
    }

    /**
     * @param $aid
     * @param $type
     * @param $userid
     * @return string
     */
    function get_student_poll_details($aid, $type, $userid)
    {
        $list = "";
        $old_answers = array();
        $pid = $this->get_article_poll_item($aid, $type);
        $q = $this->get_poll_questions($pid); // array
        foreach ($q as $qid) {
            $name = $this->get_question_name($qid);
            $ans = $this->get_poll_question_answers($qid); // array
            $student_reply = $this->get_student_reply($qid, $ans,
                $userid); // id

            $list .= "<div class='row' style='font-weight: bold;margin-bottom: 15px;'>";
            $list .= "<span class='col-md-9'>$name</span>";
            $list .= "</div>";

            foreach ($ans as $answerid) {
                $a_title = $this->get_answer_title($answerid);
                if ($type == 2) {
                    $cstatus = ($this->is_answer_correct($answerid) == 1)
                        ? 'Correct' : '';
                } // end if
                else {
                    $cstatus = '';
                }
                if ($answerid == $student_reply) {
                    array_push($old_answers, $student_reply);
                    $radio_btn
                        = "<input type='radio' class='answers' data-id='$answerid' name='$qid' checked><span style='margin-left: 15px;'>$a_title</span>";
                } // end if
                else {
                    $radio_btn
                        = "<input type='radio' class='answers' data-id='$answerid' name='$qid'><span style='margin-left: 15px;'>$a_title</span>";
                } // end else
                $list .= "<div class='row'>";
                $list .= "<span class='col-md-10' style='margin-left: 35px;'>$radio_btn</span>";
                $list .= "<span class='col-md-1'>$cstatus</span>";
                $list .= "</div>";
            } // end foreach
            $list .= "<div class='row' style='padding-top: 15px;margin-bottom: 15px;'>";
            $list .= "<span class='col-md-12'>&nbsp;</span>";
            $list .= "</div>";
        } // end foreach
        $old_answers_list = json_encode($old_answers);
        $list .= "<input type='hidden' id='old_answers' value='$old_answers_list'>";

        return $list;
    }

    /**
     * @param $item
     * @return string
     */
    function get_student_override_grades_dropbox($item)
    {
        $list = "";
        $grade = 'n/a';
        $list .= "<select id='ograde' style='width: 175px;'>";
        $query = "select * from mdl_poll_grades_override 
                where aid=$item->aid AND 
                      polid=$item->polid AND 
                      userid=$item->studentid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $grade = $row['grade'];
            }
        } // end if $num > 0
        for ($i = 0; $i <= 100; $i++) {
            $val = $i . '%';
            if ($val == $grade) {
                $list .= "<option value='$val' selected>$val</option>";
            } // end if
            else {
                $list .= "<option value='$val'>$val</option>";
            }
        }
        $list .= "</select>";

        return $list;
    }


    /**
     * @param $item
     * @return string
     */
    function get_edit_grades_dialog($item)
    {
        $teacherid = $item->teacherid;
        $groupid = $item->groupid;
        $pid = $this->get_article_poll_item($item->aid, $item->type);
        $questions = $this->get_student_poll_details($item->aid, $item->type, $item->userid);
        $udata = $this->get_user_details($item->userid);
        $item->polid = $pid;
        $item->studentid = $item->userid;
        $overridegrades = $this->get_student_override_grades_dropbox($item);
        $names = "$udata->firstname $udata->lastname";
        $img_url = $udata->pic;
        $img = "<img src='$img_url' width='213' height='160'>";

        $list = "";

        $list .= "<div class='panel panel-default'>";
        $list .= "<input type='hidden' id='studentid' value='$item->userid'>";
        $list .= "<input type='hidden' id='articleid' value='$item->aid'>";
        $list .= "<input type='hidden' id='pollid' value='$pid'>";
        $list .= "<div class='panel-heading'>Student grades details</div>";

        $list .= "<div class='panel-body'>";

        if ($img_url != '') {
            $list .= "<div class='row' style='text-align: center;'>";
            $list .= "<span class='col-md-12' >$img</span>";
            $list .= "</div>";
        }

        $list .= "<div class='row' style='text-align: center;margin-top: 15px;'>";
        $list .= "<span class='col-md-12' style='font-weight: bold;'>$names</span>";
        $list .= "</div>";

        $list .= "<div class='row' style='text-align: center;'>";
        $list .= "<span class='col-md-12' ><hr></span>";
        $list .= "</div>";


        $list .= "<div class='row'>";
        $list .= "<span class='col-md-12''>$questions</span>";
        $list .= "</div>";

        $list .= "<div class='row'>";
        $list .= "<span class='col-md-4'>Change student's grade to:</span><span>$overridegrades</span>";
        $list .= "</div>";

        $list .= "<div class='row'>";
        $list .= "<span class='col-md-1'><button class='btn btn-default'  id='update_student_grades'>Update</button></span>";
        $list .= "<span class='col-md-1'><button class='btn btn-default' data-teacherid='$teacherid' data-groupid='$groupid' id='back_to_class_grades'>Cancel</button></span>";
        $list .= "</div>";

        $list .= "</div>";

        $list .= "</div>";

        return $list;
    }

    /**
     * @param $item
     */
    function update_student_grades($item)
    {
        /*
        echo "<br><pre>";
        print_r($item);
        echo "</pre><br>";
        $ograde = $item->ograde;
        */

        /*
        $new_answers = $item->replies;
        $old_answers = json_decode($item->old_answers);
        $userid = $item->studentid;
        for ($i = 0; $i <= count($new_answers); $i++) {
            $index = $this->get_student_reply_index_id($userid,
                $old_answers[$i]);
            $this->update_student_grades_done($index, $new_answers[$i]);
        }
        */

        if ($item->ograde != '') {
            $this->set_student_override_grades($item);
        }


    }

    /**
     * @param $item
     */
    function set_student_override_grades($item)
    {
        $num = $this->is_override_grade_exists($item);
        if ($num > 0) {
            $query = "update mdl_poll_grades_override 
                    set grade='$item->ograde' 
                    where aid=$item->aid AND 
                          polid=$item->polid AND 
                          userid=$item->studentid";
        } // end if
        else {
            $query = "insert into mdl_poll_grades_override 
                    (aid, polid, userid, grade) 
                    values ($item->aid,
                            $item->polid,
                            $item->studentid,
                            '$item->ograde')";
        }
        echo "Update Grades Query: " . $query . "<br>";
        $this->db->query($query);
    }

    /**
     * @param $item
     * @return int
     */
    function is_override_grade_exists($item)
    {
        $query = "select * from mdl_poll_grades_override 
                where aid=$item->aid and 
                polid=$item->polid and 
                userid=$item->studentid";
        //echo "Is override grade exists query: " . $query . "<br>";
        $num = $this->db->numrows($query);
        return $num;
    }


    /**
     * @param $item
     * @return mixed
     */
    function get_student_override_grade($item)
    {
        $query = "select * from mdl_poll_grades_override 
                where aid=$item->aid and 
                polid=$item->polid and 
                userid=$item->studentid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $grade = $row['grade'];
        }
        return $grade;
    }


    /**
     * @param $userid
     * @param $aid
     * @return mixed
     */
    function get_student_reply_index_id($userid, $aid)
    {
        $query
            = "select * from mdl_poll_student_answers 
                where userid=$userid and aid=$aid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $index = $row['id'];
        }

        return $index;
    }

    /**
     * @param $index
     * @param $aid
     */
    function update_student_grades_done($index, $aid)
    {
        $query = "update mdl_poll_student_answers set aid=$aid where id=$index";
        $this->db->query($query);
    }

    /**
     * @param $item
     * @return string
     */
    function get_add_assistance_dialog($item)
    {

        $list = "";
        $id = $item->id;
        $list .= " <div id='$id' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Add New Assistant</h4>
                  </div>
                  <div class='modal-body'>
                 
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Assistant FirstName*</div>
                    <div class='col-sm-3'><input type='text' id='fname_$id'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Assistant LastName*</div>
                    <div class='col-sm-3'><input type='text' id='lname_$id'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Assistant Email*</div>
                    <div class='col-sm-3'><input type='text' id='email_$id'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Assistant Password*</div>
                    <div class='col-sm-3'><input type='password' id='pwd_$id'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-6' style='color: red;' id='ass_err_$id'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='add_assistance_done_$id'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $userid
     * @return mixed
     */
    function is_teacher_level($userid)
    {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $parent = $row['parent'];
        }

        return $parent;
    }

    /**
     * @param $user
     * @param $pwd
     * @return mixed
     */
    function create_user($user, $pwd)
    {
        $query = "insert into mdl_user (confirmed, mnethostid, username, password) 
              values (1, 1, '$user->email', '$pwd')";
        $this->db->query($query);
        $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
        $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
        $lastId = $lastid_arr[0];

        return $lastId;
    }

    /**
     * @param $user
     */
    function add_new_assistant($user)
    {
        $roleid = 4; // non-editing teacher
        $encpwd = password_hash($user->pwd, PASSWORD_DEFAULT);
        $userid = $this->create_user($user, $encpwd);
        $this->enrol_user($userid, $roleid);
        $this->add_to_group($user->groupid, $userid);
        $this->update_assistance_profile($user, $userid, $user->teacherid);
        $this->set_user_password($user, $userid);
        $subject = 'Assistant Account Created';
        $message = $this->get_assistance_confirmation_message($user);
        $this->send_email($subject, $message, $user->email);
    }

    /**
     * @param $gid
     * @return string
     */
    function export_class_grades($gid)
    {
        $columns = array();
        $articles = $this->get_articles_list(); // array
        $columns[] = 'Student Name';
        foreach ($articles as $aid) {
            $aname = $this->get_article_name_by_id($aid);
            $columns[] = "$aname-poll";
            $columns[] = "$aname-quiz";
            $columns[] = "$aname-board";
        }

        $groupame = $this->get_group_name($gid);
        $file = "$groupame.csv";
        $path = $_SERVER['DOCUMENT_ROOT'] . "/lms/custom/tutors/$file";
        $fp = fopen($path, 'w');

        fputcsv($fp, $columns);

        $users = $this->get_group_users($gid);  // array
        if (count($users) > 0) {
            foreach ($users as $userid) {
                $students = array();
                $udata = $this->get_user_details($userid);
                $student_names = "$udata->firstname $udata->lastname";
                $students[] = $student_names;
                foreach ($articles as $aid) {
                    $student_poll_grades
                        = $this->get_student_article_poll_grades($aid,
                        $userid, 1, false);
                    $students[] = $student_poll_grades;
                    $student_quiz_grades
                        = $this->get_student_article_poll_grades($aid,
                        $userid, 2, false);
                    $students[] = $student_quiz_grades;
                    $student_forum_grades
                        = $this->get_student_article_forum_grades($aid,
                        $userid, false);
                    $students[] = $student_forum_grades;
                } // end foreach articles
                fputcsv($fp, $students);
            } // end foreach users
        } // end if count($users)>0

        fclose($fp);

        return $file;
    }

    /**
     * @param $user
     * @return string
     */
    function get_assistance_confirmation_message($user)
    {
        $list = "";
        $fname = $user->firstname;
        $lname = $user->lastname;
        $email = $user->email;
        $pwd = $user->pwd;

        $query = "select * from mdl_email_templates where id=4";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $template = $row['template_content'];
        }

        $search = array('{firstname}', '{lastname}', '{email}', '{password}');
        $replace = array($fname, $lname, $email, $pwd);
        $list .= str_replace($search, $replace, $template);

        /*
        $list .= "<html>";
        $list .= "<body>";
        $list .= "<table>";
        $list .= "<tr><td colspan='2' style='padding: 15px;text-align: center;'>Dear $fname $lname!</td></tr>";
        $list .= "<tr><td colspan='2' style='padding: 15px;text-align: center;'>Your assistance account was successfuly created</td></tr>";
        $list .= "<tr><td style='padding: 15px;'>Your username:</td><td style='padding: 15px;'>$email</td></tr>";
        $list .= "<tr><td style='padding: 15px;'>Your password:</td><td style='padding: 15px;'>$pwd</td></tr>";
        $list .= "<tr><td colspan='2' style='padding: 15px;text-align: center;'>If you need assistance please contact us by email info@newsfactsandanalysis.com</td></tr>";
        $list .= "</table>";
        $list .= "</body>";
        $list .= "</html>";
        */

        return $list;
    }

    /**
     * @param $user
     * @param $userid
     * @param $teacherid
     */
    function update_assistance_profile($user, $userid, $teacherid)
    {
        $query = "update mdl_user 
                  set firstname='$user->firstname', 
                  lastname='$user->lastname', 
                  email='$user->email', 
                  policyagreed='1' , 
                  parent=$teacherid 
                  where id=$userid";
        $this->db->query($query);
    }

    /**
     * @param $bid
     * @return mixed
     */
    function get_board_name($bid)
    {
        $query = "select * from mdl_board where id=$bid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $title = $row['title'];
        }

        return $title;
    }

    /**
     * @param $id
     * @return stdClass
     */
    function get_post_details($id)
    {
        $query = "select * from mdl_board_posts where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            $item->post = $row['post'];
            $item->added = date('m-d-Y', $row['added']);
        }

        return $item;
    }

    /**
     * @param $item
     * @return string
     */
    function get_student_posts_details($item)
    {

        $bid = $this->get_board_id($item->aid);
        $bname = $this->get_board_name($bid);
        $posts = $this->get_student_board_posts($bid, $item->userid);

        $teacherid = $item->teacherid;
        $groupid = $item->groupid;

        $udata = $this->get_user_details($item->userid);
        $names = "$udata->firstname $udata->lastname";
        $img_url = $udata->pic;
        $img = "<img src='$img_url' width='213' height='160'>";

        $list = "";

        $list .= "<div class='panel panel-default'>";

        $list .= "<div class='panel-heading'>Student posts details</div>";

        $list .= "<div class='panel-body'>";

        if ($img_url != '') {
            $list .= "<div class='row' style='text-align: center;'>";
            $list .= "<span class='col-md-12' >$img</span>";
            $list .= "</div>";
        }

        $list .= "<div class='row' style='text-align: center;margin-top: 15px;'>";
        $list .= "<span class='col-md-12' style='font-weight: bold;'>$names</span>";
        $list .= "</div>";

        $list .= "<div class='row' style='text-align: center;'>";
        $list .= "<span class='col-md-12' ><hr></span>";
        $list .= "</div>";

        $list .= "<div class='row'>";
        $list .= "<span class='col-md-9' style='font-weight:bold'>$bname</span>";
        $list .= "</div>";

        foreach ($posts as $postid) {
            $pdata = $this->get_post_details($postid);
            $post = $pdata->post;
            $date = $pdata->added;

            $list .= "<div class='row'>";

            $list .= "<span class='col-md-6' style='padding-left: 35px;'>$post</span>";
            $list .= "<span class='col-md-3' >$date</span>";
            $list .= "</div>";
        }

        $list .= "<div class='row' style='padding-top: 35px;'>";

        $list .= "<span class='col-md-1'><button class='btn btn-default' data-teacherid='$teacherid' data-groupid='$groupid' id='back_to_class_grades'>Cancel</button></span>";
        $list .= "</div>";

        $list .= "</div>";

        $list .= "</div>";

        return $list;
    }


    /**
     * @param $userid
     * @return string
     */
    function update_teachers_classes_list($userid)
    {
        $groups = $this->get_user_groups($userid);
        $groups_dropdown = $this->get_teacher_groups_dropdown($groups);
        return $groups_dropdown;
    }

    /**
     * @param $userid
     * @return string
     */
    function get_grades_pageV2($userid)
    {
        $list = "";
        $p = new Payment();
        $mobile = $_COOKIE['mobile'];
        $dateObj = $p->get_key_expiration_dates();
        $date1 = $dateObj->start;
        $date2 = $dateObj->end;
        $roleid = $this->get_user_role_by_id($userid);
        $groups = $this->get_user_groups_by_userid($userid);
        $groups_dropdown = $this->get_teacher_groups_dropdown($groups);
        if ($roleid < 5) {
            if ($mobile) {

                $list .= "<div style='margin-left: 10%;margin-bottom:15px;text-align: center;font-weight: bold;font-size: 25px;'>";
                $list .= "This feature is not supported at mobile devices";
                $list .= "</div>";
                /*
                $list .= "<div style='margin-left: 10%;margin-bottom:15px;'>";
                $list .= "<span class='col-md-3' id='teacher_classes_container'>$groups_dropdown</span>";
                $list .= "</div>";

                $list .= "<div style='margin-left: 10%;margin-bottom:15px;'>";
                $list .= "<span class='col-md-2'><button class='btn btn-default' id='add_new_class'>Add New Class</button></span>";
                $list .= "</div>";

                $list .= "<div style='margin-left: 10%;margin-bottom:15px;'>";
                $list .= "<span class='col-md-2' id='export_grades_container' style='display: none;'><button class='btn btn-default' id='export_class_grades'>Export Grades</button></span>";
                $list .= "</div>";

                $list .= "<div style='margin-left: 10%;margin-bottom:15px;'>";
                $list .= "<span class='col-md-2' id='ast_container' style='display: none;'><button class='btn btn-default' id='add_assistance'>Add Assistant</button></span>";
                $list .= "</div>";

                $list .= "<div style='margin-left: 10%;margin-bottom:15px;'>";
                $list .= "<span class='col-md-2'><button class='btn btn-default' id='share_info'>Share NewsFacts & Analysis</button></span";
                $list .= "</div>";

                $list .= "<div style='margin-left: 10%;margin-bottom:15px;'>";
                $list .= "<span class='col-md-12' id='class_grades_container' style='width: 100%;overflow: scroll;height:auto;'></span>";
                $list .= "</div>";
                */

            } // end if
            else {
                $list .= "<div style='margin-bottom: 45px;'>";
                $list .= "<span class='col-md-3' id='teacher_classes_container'>$groups_dropdown</span>";
                $list .= "<span class='col-md-2'><button class='btn btn-default' id='add_new_class'>Add New Class</button></span>";
                $list .= "<span class='col-md-2' id='export_grades_container' style='display: none;'><button class='btn btn-default' id='export_class_grades'>Export Grades</button></span>";
                $list .= "<span class='col-md-2' id='ast_container' style='display: none;'><button class='btn btn-default' id='add_assistance'>Add Assistant</button></span>";
                $list .= "<span class='col-md-2'><button class='btn btn-default' id='share_info'>Share NewsFacts & Analysis</button></span>";
                $list .= "</div>";
                $list .= "<div class='row'>";
                $list .= "<span class='col-md-12' id='class_grades_container' style='width: 945px;overflow: scroll;height:auto;'></span>";
                $list .= "</div>";
            }
        } // end if $roleid < 5
        else {
            $list .= "<p style='text-align: center;font-weight: bold;font-size: larger;'>Subscription Start Date $date1 <br>Subscription End Date $date2</p>";
            $list .= $this->get_student_grades($userid);
        }  // end else

        return $list;
    }

    /**
     * @param $item
     * @return string
     */
    function grades_get_send_message_dialog($item)
    {
        $list = "";
        $teacherid = $item->userid;
        $emails = $item->emails;
        $id = $item->id;
        $list .= " <div id='$id' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Send Grades Feedback</h4>
                  </div>
                  <div class='modal-body'>
                    <input type='hidden' id='teacherid' value='$teacherid'>
                    <input type='hidden' id='emails_$id' value='$emails'>
                  
                    <div class='container-fluid' style='text-align:left;margin-bottom: 10px;'>
                    <div class='col-sm-2'>Subject*</div>
                    <div class='col-sm-6'><input type='text' id='subject_$id' placeholder='Subject' style='width: 375px;'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;margin-bottom: 10px;'>
                    <div class='col-sm-2'>Message*</div>
                    <div class='col-sm-6'><textarea id='msg_$id' style='width: 375px;' rows='7'></textarea></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-8' style='color: red;' id='send_grade_err_$id'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='send_grade_comment_$id'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $item
     */
    function send_grades_feedback($item)
    {
        $teacherid = $item->teacherid;
        $teacher = $this->get_user_details($teacherid);
        $fname = $teacher->firstname;
        $lname = $teacher->lastname;
        $emails = explode(',', $item->emails);
        $subject = $item->subject;
        $msg = $item->msg;
        if (count($emails) > 0) {
            $list = "";
            $list .= "<html>";
            $list .= "<body>";
            $list .= "<p style='text-align: justify'>$msg</p>";
            $list .= "<p>$fname $lname</p>";
            $list .= "</body>";
            $list .= "</html>";
            foreach ($emails as $email) {
                $this->send_email($subject, $list, $email);
            }
        } // end if ount($emails)>0
    }

    /**
     * @param $userid
     * @return string
     */
    function get_share_info_dialog($item)
    {
        $list = "";
        $id = $item->id;
        $userid = $item->userid;
        $list .= " <div id='$id' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Share this page</h4>
                  </div>
                  <div class='modal-body'>
                    <input type='hidden' id='teacherid' value='$userid'>
            
                    <div class='container-fluid' style='text-align:left;margin-bottom: 10px;'>
                    <div class='col-sm-2'>Subject*</div>
                    <div class='col-sm-6'><input type='text' id='subject_$id' placeholder='Subject' style='width: 375px;' value='Thought you’d find this useful'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;margin-bottom: 10px;'>
                    <div class='col-sm-2'>Recipient*</div>
                    <div class='col-sm-6'><input type='email' id='email_$id' placeholder='Email' style='width: 375px;'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;margin-bottom: 10px;'>
                    <div class='col-sm-2'>Message*</div>
                    <div class='col-sm-6'><textarea id='msg_$id' style='width: 375px;' rows='7'>NewsFacts & Analysis https://www.newsfactsandanalysis.com</textarea></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-6' style='color: red;' id='share_err_$id'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='send_share_info_$id'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $item
     */
    function send_share_info($item)
    {
        $udata = $this->get_user_details($item->userid);
        $names = "$udata->firstname $udata->lastname";
        $msg = $item->msg . "<br><br>$names";
        $this->send_email($item->subject, $msg, $item->recipient, false);
    }

    /**
     * @param $item
     * @return string
     */
    function get_add_new_class_dialog($item)
    {
        $list = "";
        $id = $item->id;

        $list .= " <div id='$id' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Add New Class</h4>
                  </div>
                  <div class='modal-body'>
                 
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Class Name*</div>
                    <div class='col-sm-3'><input type='text' id='gname_$id'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-6' style='color: red;' id='gname_err_$id'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='add_new_class_done_$id'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    /**
     * @param $item
     */
    function add_new_class_done($item)
    {
        /*
        echo "<pre>";
        print_r($item);
        echo "</pre>";
        */

        $now = time();
        $clearname = addslashes($item->gname);

        $query = "insert into mdl_groups (courseid,name,timecreated) 
                values ($this->courseid,'$clearname', '$now')";
        $this->db->query($query);

        $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
        $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
        $groupID = $lastid_arr[0];

        $query = "insert into mdl_groups_members (groupid, userid, timeadded) 
                values ($groupID, $item->userid, '$now')";
        $this->db->query($query);
    }

    /**
     * @param $groups
     * @return string
     */
    function get_teacher_groups_dropdown($groups)
    {
        $list = "";

        $list .= "<select id='teacher_groups' style='width:175px;'>";
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
    function get_student_grades($userid)
    {
        $list = "";

        $groups = $this->get_user_groups_by_userid($userid);
        if (count($groups) > 1) {
            $groups_dropdown = $this->get_teacher_groups_dropdown($groups);
            $list .= "<div class='row' style='margin-bottom: 45px;'>";
            $list .= "<span class='col-md-3'>$groups_dropdown</span>";
            $list .= "</div>";
        } // end if
        else {
            $item = new stdClass();
            $item->userid = $userid;
            $grades = $this->get_teacher_class_grades_table($item);
        }
        $list .= "<div class='row' >";
        $list .= "<span class='col-md-12' id='class_grades_container'>$grades</span>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $item
     * @return string
     */
    function get_teacher_class_grades_table($item)
    {
        $list = "";
        $articles = $this->get_articles_list();
        $roleid = $this->get_user_role_by_id($item->userid);
        if ($roleid == 4) {
            $users = $this->get_group_users($item->groupid, 5);
            $this->export_class_grades($item->groupid);
            $list .= $this->create_teacher_grades_table($articles, $users, $item->groupid);
        } // end if
        else {
            $list .= $this->create_student_grades_table($articles, $item->userid);
        } // end else

        return $list;
    }

    /**
     * @param $articles
     * @param $userid
     * @return string
     */
    function create_student_grades_table($articles, $userid)
    {
        $list = "";
        if (count($articles) > 0) {
            $mobile = $_COOKIE['mobile'];
            $udata = $this->get_user_details($userid);
            $names = "$udata->firstname $udata->lastname";
            $img_url = $udata->pic;
            $img = "<img src='$img_url' width='213' height='160'>";

            if ($img_url != '') {
                $list .= "<div class='row' style='text-align: center;'>";
                $list .= "<span class='col-md-12' >$img</span>";
                $list .= "</div>";
            }

            $list .= "<div class='row' style='text-align: center;margin-top: 15px;'>";
            $list .= "<span class='col-md-12' style='font-weight: bold;'>$names</span>";
            $list .= "</div>";

            $list .= "<div class='row' style='text-align: center;'>";
            $list .= "<span class='col-md-12' ><hr></span>";
            $list .= "</div>";

            if ($mobile) {
                foreach ($articles as $aid) {
                    $aname = $this->get_article_name_by_id($aid);
                    $student_poll_grades
                        = $this->get_student_article_poll_grades($aid,
                        $userid, 1, false);
                    $student_quiz_grades
                        = $this->get_student_article_poll_grades($aid,
                        $userid, 2, false);
                    $student_forum_grades
                        = $this->get_student_article_forum_grades($aid,
                        $userid, false);

                    $list .= "<div style='text-align: center;'>";
                    $list .= "<div style='padding-left: 10%;font-weight: bold;'>$aname</div>";
                    $list .= "<div style='padding-left: 10%'>Poll grades: $student_poll_grades</div>";
                    $list .= "<div style='padding-left: 10%'>Quiz grades: $student_quiz_grades</div>";
                    $list .= "<div style='padding-left: 10%'>Forum posts: $student_forum_grades</div>";
                    $list .= "</div><br>";
                } // end foreach
            } // end if
            else {
                $list .= "<table id='grades_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

                $list .= "<thead>";
                $list .= "<tr>";
                $list .= "<th>Student</th>";

                foreach ($articles as $aid) {
                    $columns = $this->get_article_table_columns($aid);
                    $list .= $columns;
                } // end foreach articles

                $list .= "</tr>";
                $list .= "</thead>";


                $udata = $this->get_user_details($userid);
                $student_names = "$udata->firstname $udata->lastname";

                $list .= "<tr>";
                $list .= "<td>$student_names</td>";

                foreach ($articles as $aid) {

                    $student_poll_grades
                        = $this->get_student_article_poll_grades($aid,
                        $userid, 1, false);
                    $student_quiz_grades
                        = $this->get_student_article_poll_grades($aid,
                        $userid, 2, false);
                    $student_forum_grades
                        = $this->get_student_article_forum_grades($aid,
                        $userid, false);

                    $list .= "<td>$student_poll_grades</td>";
                    $list .= "<td>$student_quiz_grades</td>";
                    $list .= "<td>$student_forum_grades</td>";

                } // end foreach
                $list .= "</tr>";
                $list .= "</tbody>";
                $list .= "</table>";
            }
        } // end if count($articles)>0
        else {
            $list .= "<div class='row' style='text-align: center;'>";
            $list .= "<span class='col-md-12'>There are no grades available for this class</span>";
            $list .= "</div>";
        }

        return $list;
    }

    /**
     * @param $item
     * @return string
     */
    function delete_assistant($item)
    {
        $list = "";

        $query = "update mdl_user set deleted=1 where id=$item->assistantid";
        $this->db->query($query);
        $list .= $this->get_teacher_assistances_table($item->teacherid, $item->groupid);

        return $list;
    }

    /**
     * @param $aid
     * @return string
     */
    function get_article_table_columns($aid)
    {
        $list = "";
        $mobile = $_COOKIE['mobile'];
        if ($mobile) {

        } // end if
        else {
            $aname = $this->get_article_name_by_id($aid);
            $list .= "<th>$aname-poll</th>";
            $list .= "<th>$aname-quiz</th>";
            $list .= "<th>$aname-board</th>";
        }
        return $list;
    }

    /**
     * @param $groupid
     * @param $userid
     * @return int
     */
    function is_group_participant($groupid, $userid)
    {
        $query = "select * from mdl_groups_members 
              where groupid=$groupid and userid=$userid";
        $num = $this->db->numrows($query);
        return $num;
    }


    /**
     * @param $teacherid
     * @param $groupid
     * @return string
     */
    function get_teacher_assistances_table($teacherid, $groupid)
    {
        $list = "";
        $mobile = $_COOKIE['mobile'];
        if ($mobile) {
            $list .= "<div style='font-weight: bold;font-size:25px'>";
            $list .= "Assistances list";
            $list .= "</div>";
            $query = "SELECT u.id, u.deleted, u.parent, g.groupid, g.userid FROM mdl_groups_members g, 
                  mdl_user u WHERE u.parent=$teacherid and g.groupid=$groupid and u.deleted=0
                  and u.id=g.userid";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $assistantid = $row['id'];
                    $udata = $this->get_user_details($assistantid);
                    $fname = $udata->firstname;
                    $lname = $udata->lastname;
                    $email = $udata->email;
                    $list .= "<div>";
                    $list .= "<div>$fname</div>";
                    $list .= "<div>$lname</div>";
                    $list .= "<div>$email</div>";
                    $list .= "<div><button class='btn btn-default' id='assistant_id_$assistantid'>Delete</button></div>";
                    $list .= "</div>";
                } // end while
            } // end if $num>0
        } // end if
        else {
            $list .= "<div class='row'>";
            $list .= "<span class='col-md-12' style='text-align: center;font-weight: bold;'>Assistances list</span>";
            $list .= "</div>";

            $list .= "<div class='row'>";
            $list .= "<span class='col-md-12'>";

            $list .= "<table id='assistants_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th>Firstname</th>";
            $list .= "<th>Lastname</th>";
            $list .= "<th>Email</th>";
            $list .= "<th>Ops</th>";
            $list .= "</tr>";
            $list .= "</thead>";

            $list .= "<tbody>";

            $query = "SELECT u.id, u.deleted, u.parent, g.groupid, g.userid FROM mdl_groups_members g, 
                  mdl_user u WHERE u.parent=$teacherid and g.groupid=$groupid and u.deleted=0
                  and u.id=g.userid";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $assistantid = $row['id'];
                    $udata = $this->get_user_details($assistantid);
                    $fname = $udata->firstname;
                    $lname = $udata->lastname;
                    $email = $udata->email;
                    $list .= "<tr>";
                    $list .= "<td>$fname</td>";
                    $list .= "<td>$lname</td>";
                    $list .= "<td>$email</td>";
                    $list .= "<td><button class='btn btn-default' id='assistant_id_$assistantid'>Delete</button></td>";
                    $list .= "</tr>";
                } // end while
            } // end if $num>0

            $list .= "</tbody>";

            $list .= "</table>";

            $list .= "</span>";
            $list .= "</div>";
        }

        return $list;
    }

    /**
     * @return string
     */
    function get_groups_to_move_dropdwn()
    {
        $list = "";
        $list .= "<select id='move_groups'>";
        $list .= "<option value='0' selected>Please select</option>";
        $groups = $this->get_user_groups_by_userid($_COOKIE['userid']);
        foreach ($groups as $groupid) {
            $groupname = $this->get_group_name($groupid);
            $list .= "<option value='$groupid'>$groupname</option>";
        }
        $list .= "</select>";

        return $list;
    }


    /**
     * @param $students
     */
    function delete_student($item)
    {
        $groupid = $item->groupid;
        $students = $item->students;

        if (count($students) > 0) {
            foreach ($students as $email) {
                $userid = $this->get_user_id($email);
                $query = "delete from mdl_groups_members 
                          where groupid=$groupid 
                          and userid=$userid";
                $this->db->query($query);
            }
        }
    }

    /**
     * @param $item
     */
    function move_students($item)
    {
        $oldgroup = $item->oldgroup;
        $newgroup = $item->newgroup;
        $students_arr = $item->students;
        if (count($students_arr) > 0) {
            foreach ($students_arr as $email) {
                $userid = $this->get_user_id($email);
                $this->delete_from_group($oldgroup, $userid);
                $this->add_student_to_group($newgroup, $userid);
            }
        }
    }


    /**
     * @param $articles
     * @param $users
     * @return string
     */
    function create_teacher_grades_table($articles, $users, $groupid)
    {
        $list = "";

        $userid = $_COOKIE['userid'];
        $mobile = $_COOKIE['mobile'];

        $assistances_table = $this->get_teacher_assistances_table($userid, $groupid);
        $total = count($articles);

        if (count($articles) > 0) {

            $movegroups = $this->get_groups_to_move_dropdwn();
            $list .= "<div class='row' style='margin-bottom: 15px;text-align: center;'>";
            $list .= "<span class='col-md-12' id='assistant_table_container'>$assistances_table</span>";
            $list .= "</div>";

            $list .= "<div class='row' style='margin-bottom: 15px;text-align: center;'>";
            $list .= "<span class='col-md-3'><button class='btn btn-default' id='grades_get_send_message_dialog'>Send Grades Feedback</button></span>";
            $list .= "<span class='col-md-3'>Move student to class:</span>";
            $list .= "<span class='col-md-2'>$movegroups</span>";
            $list .= "<span class='col-md-2'><button class='btn btn-default' id='move_student_btn'>Move</button></span>";
            $list .= "<span class='col-md-2'><button class='btn btn-default' id='delete_student_btn'>Delete</button></span>";
            $list .= "</div>";

            $list .= "<table id='grades_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th>Student</th>";

            foreach ($articles as $aid) {
                $columns = $this->get_article_table_columns($aid);
                $list .= $columns;
            } // end foreach articles

            $list .= "<th>Email</th>";
            $list .= "<th><input type='checkbox' id='select_all' style='margin-right: 15px;'>All</th>";

            $list .= "</tr>";
            $list .= "</thead>";

            if (count($users) > 0) {
                $list .= "<tbody>";


                foreach ($users as $userid) {

                    $udata = $this->get_user_details($userid);
                    $student_names = "$udata->firstname $udata->lastname";
                    $email = $udata->email;
                    $list .= "<tr>";
                    $list .= "<td>$student_names</td>";

                    foreach ($articles as $aid) {

                        $student_poll_grades
                            = $this->get_student_article_poll_grades($aid,
                            $userid, 1);
                        $student_quiz_grades
                            = $this->get_student_article_poll_grades($aid,
                            $userid, 2);
                        $student_forum_grades
                            = $this->get_student_article_forum_grades($aid,
                            $userid);

                        $list .= "<td>$student_poll_grades</td>";
                        $list .= "<td>$student_quiz_grades</td>";
                        $list .= "<td>$student_forum_grades</td>";

                    } // end foreach

                    $list .= "<td>$email</td>";
                    $list .= "<td><input type='checkbox' class='students' value='$email' style='margin-right: 15px;'></td>";

                    $list .= "</tr>";
                } // end foreach users

                $list .= "</tbody>";
            } // end if count($users)>0

            $list .= "</table>";

        } // end if count($articles)>0
        else {
            $list .= "<div class='row' style='text-align: center;'>";
            $list .= "<span class='col-md-12'>There are no grades available for this class</span>";
            $list .= "</div>";
        }

        return $list;
    }

    /**
     * @param $userid
     * @return mixed
     */
    function is_student_has_ppicture($userid)
    {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $pic = $row['pic'];
        }

        return $pic;
    }

    /**
     * @param $userid
     * @return string
     */
    function get_upload_dialog($userid)
    {
        $list = "";

        $list .= " <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    <input type='hidden' id='studentid' value='$userid'>
                    <h4 class='modal-title'>Upload picture</h4>
                  </div>
                  <div class='modal-body'>
                 
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Please upload picture*</div>
                    <div class='col-sm-3'><input type='hidden' role='uploadcare-uploader' name='my_file' id='my_file' /></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-6' style='color: red;' id='upload_err'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer' style='text-align: left;'>
                    <button type='button' class='btn btn-default' id='upload_my_image'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>
              </div>
            </div>";

        return $list;
    }

    /**
     * @param $item
     */
    function upload_user_picture($item)
    {
        $query = "update mdl_user set pic='$item->file_url' 
                  where id=$item->userid";
        $this->db->query($query);
    }

    /**
     * @param $userid
     *
     * @return string
     */
    function get_student_preface_block($userid)
    {
        $list = "";
        $user = $this->get_user_details($userid);
        $name = $user->firstname . ' ' . $user->lastname;
        $gorupid = $this->get_postuser_group($userid);
        $groupname = $this->get_group_name($gorupid);

        $list .= "<div class='row' style='font-weight:bold;text-align: left;margin-bottom: 15px; '>";
        $list .= "<span class='col-md-12' style=''>$name (class: $groupname)</span>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $userid
     *
     * @return int
     */
    function get_postuser_group($userid)
    {
        $query = "select * from mdl_groups_members where userid=$userid";
        //echo "Query: " . $query . "<br>";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $groupid = $row['groupid'];
        }
        if (isset($groupid)) {
            return $groupid;
        } // end if
        else {
            $groupid = 0;

            return $groupid;
        }
    }

    /**
     * @param $qid
     *
     * @return int
     */
    function get_item_total_count($qid)
    {
        $query = "select * from mdl_poll_a where $qid=$qid";
        $num = $this->db->numrows($query);

        return $num;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_answer_question_id($id)
    {
        $aid = $this->get_question_answerid($id);
        $query = "select * from mdl_poll_a where id=$aid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $qid = $row['qid'];
        }

        return $qid;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_question_answerid($id)
    {
        $query = "select * from mdl_poll_student_answers where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $aid = $row['aid'];
        }

        return $aid;
    }

    /**
     * @param $users
     *
     * @return string
     */
    function create_grades_page($users)
    {
        $list = "";
        $list .= $this->create_grade_pages_new($users);

        return $list;
    }

    /**
     * @param $users
     *
     * @return string
     */
    function create_grade_pages_new($users)
    {
        $list = "";

        $list .= "<table id='grades_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Student</th>";
        $list .= "<th>Class</th>";
        $list .= "<th>Scores</th>";
        $list .= "</tr>";
        $list .= "</thead>";

        $list .= "<tbody>";

        if (count($users) > 0) {
            foreach ($users as $userid) {
                $student = $this->get_student_block($userid);
                $gorupid = $this->get_postuser_group($userid);
                $groupname = $this->get_group_name($gorupid);
                $grades = $this->prepare_student_grades_block($userid);

                $list .= "<tr>";
                $list .= "<td>$student</td>";
                $list .= "<td>$groupname</td>";
                $list .= "<td>$grades</td>";
                $list .= "</tr>";
            } // end foreach
        } // end if count($users)>0

        $list .= "</tbody>";

        $list .= "</table>";

        return $list;
    }

    /**
     * @param $userid
     *
     * @return string
     */
    function get_student_block($userid)
    {
        $list = "";
        $user = $this->get_user_details($userid);
        $name = $user->firstname . ' ' . $user->lastname;
        $roleid = $this->get_user_role_by_id($userid);
        $status = ($roleid == 5) ? 'Student' : 'Teacher';
        $list .= $name . "<br>($status)";

        return $list;
    }

    /**
     * @param $userid
     *
     * @return string
     */
    function prepare_student_grades_block($userid)
    {
        $list = "";

        $grades = $this->get_individual_student_grades($userid);
        $list .= "<div class='row'>";
        $list .= "<span class='col-md-12'>$grades</span>";
        $list .= "</div>";

        $list .= "<div class='row'>";
        $list .= "<span class='col-mdd-12'><hr/></span>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $userid
     *
     * @return string
     */
    function get_individual_student_grades($userid)
    {
        $list = "";
        $articles = $this->get_articles_list();

        $list .= "<table id='student_grades_$userid' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>Article</th>";
        $list .= "<th>Poll Score</th>";
        $list .= "<th>Quiz Score</th>";
        $list .= "<th>Forum Posts</th>";
        $list .= "<th>Ops</th>";
        $list .= "</tr>";
        $list .= "</thead>";

        if (count($articles) > 0) {
            $list .= "<tbody>";
            foreach ($articles as $aid) {
                $pollGrades = $this->get_student_article_poll_grades(
                    $aid,
                    $userid, 1
                );
                $quizGrades = $this->get_student_article_poll_grades(
                    $aid,
                    $userid, 2
                );
                $foruGrades = $this->get_student_article_forum_grades(
                    $aid,
                    $userid
                );
                $articleName = $this->get_article_name_by_id($aid);
                $ops = $this->get_ops_block($aid, $userid);
                $class = $this->get_student_class($userid);
                $this->create_grade_students_data(
                    $aid, $userid, $class,
                    $pollGrades, $quizGrades, $foruGrades
                );

                $list .= "<tr>";
                $list .= "<td>$articleName</td>";
                $list .= "<td>$pollGrades</td>";
                $list .= "<td>$quizGrades</td>";
                $list .= "<td>$foruGrades</td>";
                $list .= "<td>$ops</td>";
                $list .= "</tr>";
            } // end foreach
            $list .= "</tbody>";
        } // end if count($articles)>0

        $list .= "</table>";

        return $list;
    }

    /**
     * @return array
     */
    function get_articles_list()
    {
        $articles = array();
        $now = time();
        //$query    = "select * from mdl_article where start<$now order by added desc";
        $query = "select * from mdl_article where active=1 order by added ";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $articles[] = $row['id'];
            }
        } // end if $num>0

        return $articles;
    }

    /**
     * @param $aid
     * @param $userid
     * @param $type
     *
     * @return string
     */
    function get_student_article_poll_grades(
        $aid,
        $userid,
        $type,
        $table = true
    )
    {
        $list = "";
        $score = 0;
        $pid = $this->get_article_poll_item($aid, $type);
        if ($pid > 0) {
            $item = new stdClass();
            $item->aid = $aid;
            $item->polid = $pid;
            $item->studentid = $userid;
            $override_grade_status = $this->is_override_grade_exists($item);
            if ($override_grade_status > 0) {
                $token = $userid . '_' . $aid;
                $list .= $this->get_student_override_grade($item);
                if ($type == 1) {
                    $list .= "<br> <a id='edit_poll_grades_$token' data-aid='$aid' data-type='$type' data-userid='$userid' style='cursor: pointer;'>View</a>";
                } // end if
                else {
                    $list .= "<br> <a id='edit_quiz_grades_$token' data-aid='$aid' data-type='$type' data-userid='$userid' style='cursor: pointer;'>View</a>";
                }
            } // end if $override_grade_status>0
            else {
                $q = $this->get_poll_questions($pid);
                if (count($q) > 0) {
                    foreach ($q as $qid) {
                        $a = $this->get_poll_question_answers($qid);
                        foreach ($a as $id) {
                            $an[] = $id;
                        }    // end foreach
                    } // end foreach
                    if (count($an) > 0) {
                        $sta = $this->get_student_answers($userid, $an);
                        if (count($sta) > 0) {
                            $score = $this->get_student_item_score($sta, count($q));
                        } // end if count($sta)>0
                    } // end if count($a)>0
                } // end if count($q)>0
            } // end else when no override grades
        } // end if $pid>0

        if (is_object($score)) {
            $correct = $score->correct;
            $total = $score->total;
            $pers = $score->pers;
            $token = $userid . '_' . $aid;
            if ($type == 1) {
                if ($table) {
                    $list .= " $pers % <br> <a id='edit_poll_grades_$token' data-aid='$aid' data-type='$type' data-userid='$userid' style='cursor: pointer;'>View</a>";
                } // end if
                else {
                    $list .= " $pers % ";
                } // end else
            } // end if
            else {
                if ($table) {
                    $list .= " $pers % <br> <a id='edit_quiz_grades_$token' data-aid='$aid' data-type='$type' data-userid='$userid' style='cursor: pointer;'>View</a>";
                } // end if
                else {
                    $list .= "$pers % ";
                } // end else
            } // end else
        } // end if
        else {
            $list .= $score;
        }


        return $list;
    }

    /**
     * @param $aid
     * @param $type
     *
     * @return int
     */
    function get_article_poll_item($aid, $type)
    {
        $pid = 0;
        $query = "select * from mdl_poll where aid=$aid and type=$type";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $pid = $row['id'];
            } // end while
        } // end if $num > 0

        return $pid;
    }

    /**
     * @param $pid
     *
     * @return array
     */
    function get_poll_questions($pid)
    {
        $q = array();
        $query = "select * from mdl_poll_q where pid=$pid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $q[] = $row['id'];
            } // end while
        } // end if $num > 0

        return $q;
    }

    /**
     * @param $qid
     *
     * @return array
     */
    function get_poll_question_answers($qid)
    {
        $a = array();
        $query = "select * from mdl_poll_a where qid=$qid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $a[] = $row['id'];
            } // end while
        } // end if $num > 0

        return $a;
    }

    /**
     * @param $userid
     * @param $answers
     *
     * @return array
     */
    function get_student_answers($userid, $answers)
    {
        $sta = array();
        if (count($answers) > 0) {
            $as = implode(',', $answers);
            $query
                = "select * from mdl_poll_student_answers where userid=$userid and aid in ($as)";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $sta[] = $row['id'];
                } // end while
            } // end if  $num > 0
        } // end if count($answers)>0

        return $sta;
    }

    /**
     * @param $sta
     * @param $total
     *
     * @return stdClass
     */
    function get_student_item_score($sta, $total)
    {
        $correct = 0;
        foreach ($sta as $id) {
            $aid = $this->get_student_answer_aid($id);
            $status = $this->is_answer_correct($aid);
            if ($status == 1) {
                $correct++;
            } // end if
        } // end foreach
        $pers = round(($correct / $total) * 100);
        $score = new stdClass();
        $score->correct = $correct;
        $score->total = $total;
        $score->pers = $pers;

        return $score;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_student_answer_aid($id)
    {
        $query = "select * from mdl_poll_student_answers where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $aid = $row['aid'];
        }

        return $aid;
    }

    /**
     * @param $aid
     *
     * @return mixed
     */
    function is_answer_correct($aid)
    {
        $query = "select * from mdl_poll_a where id=$aid";
        //echo "Query: " . $query . "<br>";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $correct = $row['correct'];
        }

        return $correct;
    }

    /**
     * @param $aid
     * @param $userid
     *
     * @return string
     */
    function get_student_article_forum_grades($aid, $userid, $table = true)
    {
        $list = "";
        $bid = $this->get_board_id($aid);
        $posts = $this->get_student_board_posts($bid, $userid);
        $total = count($posts);
        $token = $userid . '_' . $aid;
        if ($table) {
            $list .= "Total posts: $total <br><a href='#' onclick='return false;' id='posts_details_$token' data-aid='$aid' data-userid='$userid' style='cursor: pointer;color:#337ab7;'>View</a>";
        } // end if
        else {
            $list .= "Total posts: $total";
        } // end else

        return $list;
    }


    /**
     * @param $aid
     *
     * @return int
     */
    function get_board_id($aid)
    {
        $query = "select * from mdl_board where aid=$aid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $bid = $row['id'];
            }
        } // end if
        else {
            $bid = 0;
        }

        return $bid;
    }

    /**
     * @param $bid
     * @param $userid
     *
     * @return array
     */
    function get_student_board_posts($bid, $userid)
    {
        $posts = array();
        if ($bid > 0 and $userid > 0) {
            $query
                = "select * from mdl_board_posts where bid=$bid and userid=$userid";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $posts[] = $row['id'];
                } // end while
            } // end if $num > 0
        } // end if $bid>0 and $userid>0

        return $posts;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_article_name_by_id($id)
    {
        $query = "select * from mdl_article where id=$id ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $title = $row['title'];
        }

        return $title;
    }

    /**
     * @param $aid
     * @param $userid
     *
     * @return string
     */
    function get_ops_block($aid, $userid)
    {
        $list = "";
        $path = $userid . '_' . $aid;
        $link = "https://" . $_SERVER['SERVER_NAME']
            . "/lms/custom/common/data/grades_$path.csv";
        $list .= "<a href='$link' target='_blank'><button id='export_student_grades' data-aid='$aid' data-userid='$userid'>Export</button></a>";

        return $list;
    }

    /**
     * @param $userid
     *
     * @return mixed
     */
    function get_student_class($userid)
    {
        $gorupid = $this->get_postuser_group($userid);
        $groupname = $this->get_group_name($gorupid);

        return $groupname;
    }

    /**
     * @param $aid
     * @param $userid
     * @param $class
     * @param $pollScore
     * @param $quizScore
     * @param $forumScore
     */
    function create_grade_students_data(
        $aid,
        $userid,
        $class,
        $pollScore,
        $quizScore,
        $forumScore
    )
    {
        $gpath = $userid . '_' . $aid;
        $path = $_SERVER['DOCUMENT_ROOT']
            . "/lms/custom/common/data/grades_$gpath.csv";
        $output = fopen($path, 'w');
        $userdata = $this->get_user_details($userid);

        $data = array(
            $userdata->firstname,
            $userdata->lastname,
            $class,
            $pollScore,
            $quizScore,
            $forumScore
        );
        fputcsv($output, $data);
        fclose($path);
    }

    /**
     * @return array
     */
    function get_news_boards()
    {
        $boards = array();
        $query = "select * from mdl_board order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $b = new stdClass();
                $b->aid = $row['aid'];
                $b->bid = $row['id'];
                $boards[] = $b;
            } // end while
        } // end if $num > 0

        return $boards;
    }

    /**
     * @param $item
     */
    function get_csv_student_grades($item)
    {
        $aid = $item->aid;
        $userid = $item->userid;
        $userdata = $this->get_user_details($userid);
        $groupid = $this->get_postuser_group($userid);
        $class = $this->get_group_name($groupid);

        $pollA = $this->get_student_pol_scores($userid);
        $pollScoreData = $this->get_section_score_block($pollA, false);
        $pollScore = $pollScoreData['correct'];

        $quizA = $this->get_student_quiz_scores($userid);
        $quizScoreData = $this->get_section_score_block($quizA, false);
        $quizScore = $quizScoreData['correct'];

        $forumA = $this->get_student_forum_scores($userid, false);
        $forumScore = count($forumA);

        $path = $_SERVER['DOCUMENT_ROOT']
            . "/lms/custom/tutors/grades_$userid.csv";
        $output = fopen($path, 'w');

        $data = array(
            $userdata->firstname,
            $userdata->lastname,
            $class,
            $pollScore,
            $quizScore,
            $forumScore
        );
        fputcsv($output, $data);
        fclose($path);
    }

    /**
     * @param $userid
     *
     * @return mixed
     */
    function get_student_quiz_scores($userid)
    {
        $this->get_news_qestions(2);
        $answers = $this->get_news_student_answers($userid, 2);

        return $answers;
    }

    /**
     * @param $userid
     *
     * @return string
     */
    function get_meeting_url($userid)
    {
        $list = "";
        $today = date('m-d-Y', time());
        $groupid = $this->get_postuser_group($userid);
        $query = "select * from mdl_classes 
                  where groupid=$groupid 
                  and active=1 
                  and from_unixtime(date, '%m-%d-%Y')='$today'";
        //echo "Query: ".$query."<br>";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $groupname = $this->get_group_name($groupid);
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $title = $row['title'];
                $date = date('m-d-Y h:i:s', $row['date']);
            } // end while
            $link = "https://demo.bigbluebutton.org/b/meetings/$groupname";
            $list .= "<div class='row' style='font-weight: bold;'>";
            $list .= "<span class='col-md-12'>Todays Onlince Classes: $date $title     <a href='$link' target='_blank'><button class='btn btn-default'>Join</button></a></span>";
            $list .= "</div>";
        } // end if $num>0

        return $list;
    }

}