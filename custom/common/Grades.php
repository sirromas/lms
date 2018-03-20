<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Grades extends Utils
{

    public $users = array();
    public $news_poll_questions = array();
    public $news_quiz_questions = array();
    public $student_poll_score = array();
    public $student_quiz_score = array();
    public $student_forum_score = array();
    public $articleID;
    public $courseid;


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

    function get_question_name($qid)
    {
        $query  = "select * from mdl_poll_q where id=$qid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['title'];
        }

        return $name;
    }

    function get_answer_title($id)
    {
        $query  = "select * from mdl_poll_a where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $a = $row['a'];
        }

        return $a;
    }

    function get_student_reply($qid, $ans, $userid)
    {
        $ans_list = implode(',', $ans);
        $query
                  = "select * from mdl_poll_student_answers 
                where userid=$userid and aid in ($ans_list)";
        $result   = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $answerid = $row['aid'];
        }

        return $answerid;
    }

    function get_student_poll_details($aid, $type, $userid)
    {
        $list        = "";
        $old_answers = array();
        $pid         = $this->get_article_poll_item($aid, $type);
        $q           = $this->get_poll_questions($pid); // array
        foreach ($q as $qid) {
            $name          = $this->get_question_name($qid);
            $ans           = $this->get_poll_question_answers($qid); // array
            $student_reply = $this->get_student_reply($qid, $ans,
                $userid); // id

            $list .= "<div class='row' style='font-weight: bold;margin-bottom: 15px;'>";
            $list .= "<span class='col-md-9'>$name</span>";
            $list .= "</div>";

            foreach ($ans as $answerid) {
                $a_title = $this->get_answer_title($answerid);
                $cstatus = ($this->is_answer_correct($answerid) == 1)
                    ? 'Correct' : 'Incorrect';
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
        $list             .= "<input type='hidden' id='old_answers' value='$old_answers_list'>";

        return $list;
    }

    function get_edit_grades_dialog($item)
    {
        $teacherid = $item->teacherid;
        $groupid   = $item->groupid;
        $pid       = $this->get_article_poll_item($item->aid, $item->type);
        $aname     = $this->get_article_name_by_id($pid);
        $questions = $this->get_student_poll_details($item->aid, $item->type,
            $item->userid);
        $udata     = $this->get_user_details($item->userid);
        $names     = "$udata->firstname $udata->lastname";
        $img_url   = $udata->pic;
        $img       = "<img src='$img_url' width='213' height='160'>";

        $list = "";

        $list .= "<div class='panel panel-default'>";
        $list .= "<input type='hidden' id='studentid' value='$item->userid'>";
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
        $list .= "<span class='col-md-1'><button class='btn btn-default'  id='update_student_grades'>Update</button></span>";
        $list .= "<span class='col-md-1'><button class='btn btn-default' data-teacherid='$teacherid' data-groupid='$groupid' id='back_to_class_grades'>Cancel</button></span>";
        $list .= "</div>";

        $list .= "</div>";

        $list .= "</div>";

        return $list;
    }

    function update_student_grades($item)
    {
        $new_answers = $item->replies;
        $old_answers = json_decode($item->old_answers);
        $userid      = $item->studentid;
        for ($i = 0; $i <= count($new_answers); $i++) {
            $index = $this->get_student_reply_index_id($userid,
                $old_answers[$i]);
            $this->update_student_grades_done($index, $new_answers[$i]);
        }
    }

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

    function update_student_grades_done($index, $aid)
    {
        $query = "update mdl_poll_student_answers set aid=$aid where id=$index";
        $this->db->query($query);
    }

    function get_add_assistance_dialog($userid)
    {
        $list = "";

        $list
            .= " <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Add New Assistant</h4>
                  </div>
                  <div class='modal-body'>
                 
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Assistant FirstName*</div>
                    <div class='col-sm-3'><input type='text' id='fname'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Assistant LastName*</div>
                    <div class='col-sm-3'><input type='text' id='lname'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-5'>Assistant Email*</div>
                    <div class='col-sm-3'><input type='text' id='email'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:left;'>
                    <div class='col-sm-6' style='color: red;' id='ass_err'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='add_assistance_done'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    function is_teacher_level($userid)
    {
        $query  = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $parent = $row['parent'];
        }

        return $parent;
    }

    function create_user($user, $pwd)
    {
        $query
            = "insert into mdl_user (confirmed, mnethostid, username, password) 
              values (1, 1, '$user->email', '$pwd')";
        $this->db->query($query);
        $stmt       = $this->db->query("SELECT LAST_INSERT_ID()");
        $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
        $lastId     = $lastid_arr[0];

        return $lastId;
    }

    function add_new_assistant($user)
    {
        $roleid = 4; // non-editing teacher
        $encpwd = password_hash($user->pwd, PASSWORD_DEFAULT);
        $userid = $this->create_user($user, $encpwd);
        $this->enrol_user($userid, $roleid);
        $this->add_to_group($user->groupid, $userid);
        $this->update_assistance_profile($user, $userid, $user->teacherid);
        $subject = 'Account Creation Confirmation';
        $message = $this->get_assistance_confirmation_message($user);
        $this->send_email($subject, $message, $user->email);
    }

    function export_class_grades($gid)
    {
        $columns   = array();
        $articles  = $this->get_articles_list(); // array
        $columns[] = 'Student Name';
        foreach ($articles as $aid) {
            $aname     = $this->get_article_name_by_id($aid);
            $columns[] = "$aname-poll";
            $columns[] = "$aname-quiz";
            $columns[] = "$aname-board";
        }

        $groupame = $this->get_group_name($gid);
        $file     = "$groupame.csv";
        $path     = $_SERVER['DOCUMENT_ROOT']
            . "/lms/custom/tutors/$file";
        $fp       = fopen($path, 'w');

        fputcsv($fp, $columns);

        $users = $this->get_group_users($gid);  // array
        if (count($users) > 0) {
            foreach ($users as $userid) {
                $students      = array();
                $udata         = $this->get_user_details($userid);
                $student_names = "$udata->firstname $udata->lastname";
                $students[]    = $student_names;
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

    function get_assistance_confirmation_message($user)
    {
        $list  = "";
        $fname = $user->firstname;
        $lname = $user->lastname;
        $email = $user->email;
        $pwd   = $user->pwd;

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

        return $list;
    }

    function update_assistance_profile($user, $userid, $teacherid)
    {
        $query
            = "update mdl_user 
                  set firstname='$user->firstname', 
                  lastname='$user->lastname', 
                  email='$user->email', 
                  policyagreed='1' , 
                  parent=$teacherid 
                  where id=$userid";
        $this->db->query($query);
    }

    function get_board_name($bid)
    {
        $query  = "select * from mdl_board where id=$bid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $title = $row['title'];
        }

        return $title;
    }

    function get_post_details($id)
    {
        $query  = "select * from mdl_board_posts where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item        = new stdClass();
            $item->post  = $row['post'];
            $item->added = date('m-d-Y', $row['added']);
        }

        return $item;
    }

    function get_student_posts_details($item)
    {

        $bid   = $this->get_board_id($item->aid);
        $bname = $this->get_board_name($bid);
        $posts = $this->get_student_board_posts($bid, $item->userid);

        $teacherid = $item->teacherid;
        $groupid   = $item->groupid;

        $udata   = $this->get_user_details($item->userid);
        $names   = "$udata->firstname $udata->lastname";
        $img_url = $udata->pic;
        $img     = "<img src='$img_url' width='213' height='160'>";

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
            $post  = $pdata->post;
            $date  = $pdata->added;

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

    function get_grades_pageV2($userid)
    {
        $list = "";

        $roleid = $this->get_user_role();
        if ($roleid < 5) {
            $groups          = $this->get_user_groups();
            $groups_dropdown = $this->get_teacher_groups_dropdown($groups);
            $list            .= "<div class='row' style='margin-bottom: 45px;'>";
            $list            .= "<span class='col-md-3'>$groups_dropdown</span>";
            $list            .= "<span class='col-md-2'><button class='btn btn-default' id='add_new_class'>Add New Class</button></span>";
            $list            .= "<span class='col-md-2' id='export_grades_container' style='display: none;'><button class='btn btn-default' id='export_class_grades'>Export Grades</button></span>";
            $list            .= "<span class='col-md-2' id='ast_container' style='display: none;'><button class='btn btn-default' id='add_assistance'>Add Assistance Account</button></span>";
            $list            .= "<span class='col-md-2'><div class='sharethis-inline-share-buttons'></div></span>";
            $list            .= "</div>";
            $list            .= "<div class='row' >";
            $list            .= "<span class='col-md-12' id='class_grades_container'></span>";
            $list            .= "</div>";
        } // end if $roleid < 5
        else {
            $list .= $this->get_student_grades($userid);
        }  // end else

        return $list;
    }

    function get_add_new_class_dialog()
    {
        $list = "";

        $list
            .= " <div id='myModal' class='modal fade' role='dialog'>
              <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>
                    
                    <h4 class='modal-title'>Add New Class</h4>
                  </div>
                  <div class='modal-body'>
                 
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-3'>Class Name*</div>
                    <div class='col-sm-3'><input type='text' id='gname'></div>
                    </div>
                    
                    <div class='container-fluid' style='text-align:center;'>
                    <div class='col-sm-6' style='color: red;' id='gname_err'></div>
                    </div>
                   
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='add_new_class_done'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    function add_new_class_done($item)
    {
        /*
        echo "<pre>";
        print_r($item);
        echo "</pre>";
        */

        $now       = time();
        $clearname = addslashes($item->gname);

        $query
            = "insert into mdl_groups (courseid,name,timecreated) 
                values ($this->courseid,'$clearname', '$now')";
        $this->db->query($query);

        $stmt       = $this->db->query("SELECT LAST_INSERT_ID()");
        $lastid_arr = $stmt->fetch(PDO::FETCH_NUM);
        $groupID    = $lastid_arr[0];

        $query
            = "insert into mdl_groups_members (groupid, userid, timeadded) 
                values ($groupID, $item->userid, '$now')";
        $this->db->query($query);
    }

    function get_teacher_groups_dropdown($groups)
    {
        $list = "";

        $list .= "<select id='teacher_groups' style='width:175px;'>";
        $list .= "<option value='0' selected>Please select class</option>";
        foreach ($groups as $groupid) {
            $groupname = $this->get_group_name($groupid);
            $list      .= "<option value='$groupid'>$groupname</option>";
        }
        $list .= "</select>";

        return $list;
    }

    function get_student_grades($userid)
    {
        $list = "";

        $groups          = $this->get_user_groups();
        $groups_dropdown = $this->get_teacher_groups_dropdown($groups);
        $list            .= "<div class='row' style='margin-bottom: 45px;'>";
        $list            .= "<span class='col-md-3'>$groups_dropdown</span>";
        $list            .= "</div>";
        $list            .= "<div class='row' >";
        $list            .= "<span class='col-md-12' id='class_grades_container'></span>";
        $list            .= "</div>";

        return $list;
    }

    function get_teacher_class_grades_table($item)
    {
        $list = "";

        $articles = $this->get_articles_list();
        $roleid   = $this->get_user_role_by_id($item->userid);
        //echo "Role ID: ".$roleid."<br>";
        if ($roleid == 4) {
            $users = $this->get_group_users($item->groupid);
            $this->export_class_grades($item->groupid);
            $list .= $this->create_teacher_grades_table($articles, $users);
        } // end if
        else {
            $list .= $this->create_student_grades_table($articles,
                $item->userid);
        } // end else

        return $list;
    }

    function create_student_grades_table($articles, $userid)
    {
        $list = "";
        if (count($articles) > 0) {


            $udata   = $this->get_user_details($userid);
            $names   = "$udata->firstname $udata->lastname";
            $img_url = $udata->pic;
            $img     = "<img src='$img_url' width='213' height='160'>";

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

            $list .= "<table id='grades_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th>Student</th>";

            foreach ($articles as $aid) {
                $columns = $this->get_article_table_columns($aid);
                $list    .= $columns;
            } // end foreach articles

            $list .= "</tr>";
            $list .= "</thead>";


            $udata         = $this->get_user_details($userid);
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
        } // end if count($articles)>0
        else {
            $list .= "<div class='row' style='text-align: center;'>";
            $list .= "<span class='col-md-12'>There are no grades available for this class</span>";
            $list .= "</div>";
        }

        return $list;
    }

    function get_article_table_columns($aid)
    {
        $list  = "";
        $aname = $this->get_article_name_by_id($aid);
        $list  .= "<th>$aname-poll</th>";
        $list  .= "<th>$aname-quiz</th>";
        $list  .= "<th>$aname-board</th>";

        return $list;
    }

    function create_teacher_grades_table($articles, $users)
    {
        $list = "";

        if (count($articles) > 0) {

            $list .= "<table id='grades_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

            $list .= "<thead>";
            $list .= "<tr>";
            $list .= "<th>Student</th>";

            foreach ($articles as $aid) {
                $columns = $this->get_article_table_columns($aid);
                $list    .= $columns;
            } // end foreach articles

            $list .= "</tr>";
            $list .= "</thead>";

            if (count($users) > 0) {
                $list .= "<tbody>";


                foreach ($users as $userid) {

                    $udata         = $this->get_user_details($userid);
                    $student_names = "$udata->firstname $udata->lastname";

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

    function is_student_has_ppicture($userid)
    {
        $query  = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $pic = $row['pic'];
        }

        return $pic;
    }

    function get_upload_dialog($userid)
    {
        $list = "";

        $list
            .= " <div id='myModal' class='modal fade' role='dialog'>
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
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-default' id='upload_my_image'>Ok</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='cancel_dialog'>Cancel</button>
                  </div>
                </div>

              </div>
            </div>";

        return $list;
    }

    function upload_user_picture($item)
    {
        $query
            = "update mdl_user set pic='$item->file_url' 
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
        $list      = "";
        $user      = $this->get_user_details($userid);
        $name      = $user->firstname . ' ' . $user->lastname;
        $gorupid   = $this->get_postuser_group($userid);
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
        $num   = $this->db->numrows($query);

        return $num;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_answer_question_id($id)
    {
        $aid    = $this->get_question_answerid($id);
        $query  = "select * from mdl_poll_a where id=$aid";
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
        $query  = "select * from mdl_poll_student_answers where id=$id";
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
                $student   = $this->get_student_block($userid);
                $gorupid   = $this->get_postuser_group($userid);
                $groupname = $this->get_group_name($gorupid);
                $grades    = $this->prepare_student_grades_block($userid);

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
        $list   = "";
        $user   = $this->get_user_details($userid);
        $name   = $user->firstname . ' ' . $user->lastname;
        $roleid = $this->get_user_role_by_id($userid);
        $status = ($roleid == 5) ? 'Student' : 'Teacher';
        $list   .= $name . "<br>($status)";

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
        $list   .= "<div class='row'>";
        $list   .= "<span class='col-md-12'>$grades</span>";
        $list   .= "</div>";

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
        $list     = "";
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
                $pollGrades  = $this->get_student_article_poll_grades(
                    $aid,
                    $userid, 1
                );
                $quizGrades  = $this->get_student_article_poll_grades(
                    $aid,
                    $userid, 2
                );
                $foruGrades  = $this->get_student_article_forum_grades(
                    $aid,
                    $userid
                );
                $articleName = $this->get_article_name_by_id($aid);
                $ops         = $this->get_ops_block($aid, $userid);
                $class       = $this->get_student_class($userid);
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
        $now      = time();
        //$query    = "select * from mdl_article where start<$now order by added desc";
        $query = "select * from mdl_article where active=1 order by added ";
        $num   = $this->db->numrows($query);
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
    ) {
        $score = 'N/A';
        $list  = "";
        $pid   = $this->get_article_poll_item($aid, $type);
        if ($pid > 0) {
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
        } // end if $pid>0

        if (is_object($score)) {
            $correct = $score->correct;
            $total   = $score->total;
            $pers    = $score->pers;
            $token   = $userid . '_' . $aid;
            if ($type == 1) {
                if ($table) {
                    $list .= "$correct out of $total <br> $pers % <br> <a id='edit_poll_grades_$token' data-aid='$aid' data-type='$type' data-userid='$userid' style='cursor: pointer;'>View</a>";
                } // end if
                else {
                    $list .= "$correct out of $total $pers % ";
                } // end else
            } // end if
            else {
                if ($table) {
                    $list .= "$correct out of $total <br> $pers % <br> <a id='edit_quiz_grades_$token' data-aid='$aid' data-type='$type' data-userid='$userid' style='cursor: pointer;'>View</a>";
                } // end if
                else {
                    $list .= "$correct out of $total $pers % ";
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
        $pid   = 0;
        $query = "select * from mdl_poll where aid=$aid and type=$type";
        $num   = $this->db->numrows($query);
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
        $q     = array();
        $query = "select * from mdl_poll_q where pid=$pid";
        $num   = $this->db->numrows($query);
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
        $a     = array();
        $query = "select * from mdl_poll_a where qid=$qid";
        $num   = $this->db->numrows($query);
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
            $as  = implode(',', $answers);
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
            $aid    = $this->get_student_answer_aid($id);
            $status = $this->is_answer_correct($aid);
            if ($status == 1) {
                $correct++;
            } // end if
        } // end foreach
        $pers           = round(($correct / $total) * 100);
        $score          = new stdClass();
        $score->correct = $correct;
        $score->total   = $total;
        $score->pers    = $pers;

        return $score;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function get_student_answer_aid($id)
    {
        $query  = "select * from mdl_poll_student_answers where id=$id";
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
        $list  = "";
        $bid   = $this->get_board_id($aid);
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
        $num   = $this->db->numrows($query);
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
        $query  = "select * from mdl_article where id=$id ";
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
        $gorupid   = $this->get_postuser_group($userid);
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
    ) {
        $gpath    = $userid . '_' . $aid;
        $path     = $_SERVER['DOCUMENT_ROOT']
            . "/lms/custom/common/data/grades_$gpath.csv";
        $output   = fopen($path, 'w');
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
        $query  = "select * from mdl_board order by added desc";
        $num    = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $b        = new stdClass();
                $b->aid   = $row['aid'];
                $b->bid   = $row['id'];
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
        $aid      = $item->aid;
        $userid   = $item->userid;
        $userdata = $this->get_user_details($userid);
        $groupid  = $this->get_postuser_group($userid);
        $class    = $this->get_group_name($groupid);

        $pollA         = $this->get_student_pol_scores($userid);
        $pollScoreData = $this->get_section_score_block($pollA, false);
        $pollScore     = $pollScoreData['correct'];

        $quizA         = $this->get_student_quiz_scores($userid);
        $quizScoreData = $this->get_section_score_block($quizA, false);
        $quizScore     = $quizScoreData['correct'];

        $forumA     = $this->get_student_forum_scores($userid, false);
        $forumScore = count($forumA);

        $path   = $_SERVER['DOCUMENT_ROOT']
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
        $list    = "";
        $today   = date('m-d-Y', time());
        $groupid = $this->get_postuser_group($userid);
        $query
                 = "select * from mdl_classes 
                  where groupid=$groupid 
                  and active=1 
                  and from_unixtime(date, '%m-%d-%Y')='$today'";
        //echo "Query: ".$query."<br>";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $groupname = $this->get_group_name($groupid);
            $result    = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $title = $row['title'];
                $date  = date('m-d-Y h:i:s', $row['date']);
            } // end while
            $link = "https://demo.bigbluebutton.org/b/meetings/$groupname";
            $list .= "<div class='row' style='font-weight: bold;'>";
            $list .= "<span class='col-md-12'>Todays Onlince Classes: $date $title     <a href='$link' target='_blank'><button class='btn btn-default'>Join</button></a></span>";
            $list .= "</div>";
        } // end if $num>0

        return $list;
    }

}