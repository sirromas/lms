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

    function __construct()
    {
        parent::__construct();
    }

    function merge_group_users($group_users)
    {
        foreach ($group_users as $userid) {
            $this->users[] = $userid;
        }
    }

    function get_grades_page($userid)
    {
        $list   = "";
        $roleid = $this->get_user_role();
        if ($roleid < 5) {
            // It is teacher
            $groups = $this->get_user_groups();
            foreach ($groups as $groupid) {
                $group_users = $this->get_group_users($groupid);
                $this->merge_group_users($group_users);
            } // end foreach
        } // end if
        else {
            // It is student
            $this->users[] = $userid;
        }
        $list .= $this->create_grades_page($this->users);

        return $list;

    }

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

    function get_articles_list()
    {
        $articles = array();
        $now      = time();
        //$query    = "select * from mdl_article where start<$now order by added desc";
        $query = "select * from mdl_article order by added ";
        $num   = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $articles[] = $row['id'];
            }
        } // end if $num>0

        return $articles;
    }

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

    function get_item_total_count($qid)
    {
        $query = "select * from mdl_poll_a where $qid=$qid";
        $num   = $this->db->numrows($query);

        return $num;
    }

    function get_question_answerid($id)
    {
        $query  = "select * from mdl_poll_student_answers where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $aid = $row['aid'];
        }

        return $aid;
    }

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

    function get_student_answer_aid($id)
    {
        $query  = "select * from mdl_poll_student_answers where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $aid = $row['aid'];
        }

        return $aid;
    }

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

    function get_student_article_poll_grades($aid, $userid, $type)
    {
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
            $list    .= "$correct out of $total <br> $pers %";
        } // end if
        else {
            $list .= $score;
        }

        return $list;
    }


    function get_student_article_forum_grades($aid, $userid)
    {
        $list  = "";
        $bid   = $this->get_board_id($aid);
        $posts = $this->get_student_board_posts($bid, $userid);
        $total = count($posts);
        $list  .= "Total posts: $total";

        return $list;
    }

    function get_student_board_posts($bid, $userid)
    {
        $posts = array();
        $query
               = "select * from mdl_board_posts where bid=$bid and userid=$userid";
        $num   = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $posts[] = $row['id'];
            }
        }

        return $posts;
    }

    function get_student_class($userid)
    {
        $gorupid   = $this->get_postuser_group($userid);
        $groupname = $this->get_group_name($gorupid);

        return $groupname;
    }

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
                $pollGrades  = $this->get_student_article_poll_grades($aid,
                    $userid, 1);
                $quizGrades  = $this->get_student_article_poll_grades($aid,
                    $userid, 2);
                $foruGrades  = $this->get_student_article_forum_grades($aid,
                    $userid);
                $articleName = $this->get_article_name_by_id($aid);
                $ops         = $this->get_ops_block($aid, $userid);
                $class       = $this->get_student_class($userid);
                $this->create_grade_students_data($aid, $userid, $class,
                    $pollGrades, $quizGrades, $foruGrades);

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

    function create_grades_page($users)
    {
        $list = "";
        $list .= $this->create_grade_pages_new($users);

        return $list;
    }

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

    function get_ops_block($aid, $userid)
    {
        $list = "";
        $path = $userid . '_' . $aid;
        $link = "https://" . $_SERVER['SERVER_NAME']
            . "/lms/custom/common/data/grades_$path.csv";
        $list .= "<a href='$link' target='_blank'><button id='export_student_grades' data-aid='$aid' data-userid='$userid'>Export</button></a>";

        return $list;
    }

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

    function get_article_name_by_id($id)
    {
        $query  = "select * from mdl_article where id=$id ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $title = $row['title'];
        }

        return $title;
    }

    function get_student_quiz_scores($userid)
    {
        $this->get_news_qestions(2);
        $answers = $this->get_news_student_answers($userid, 2);

        return $answers;
    }

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

    function get_meeting_url($userid)
    {
        $list    = "";
        $today   = date('m-d-Y', time());
        $groupid = $this->get_postuser_group($userid);
        $query = "select * from mdl_classes 
                  where groupid=$groupid 
                  and active=1 
                  and from_unixtime(date, '%m-%d-%Y')='$today'";
        //echo "Query: ".$query."<br>";
        $num     = $this->db->numrows($query);
        if ($num > 0) {
            $groupname = $this->get_group_name($groupid);
            $result    = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $title = $row['title'];
                $date=date('m-d-Y h:i:s', $row['date']);
            } // end while
            $link = "https://demo.bigbluebutton.org/b/meetings/$groupname";
            $list .= "<div class='row' style='font-weight: bold;'>";
            $list .= "<span class='col-md-12'>Todays Onlince Classes: $date $title     <a href='$link' target='_blank'><button class='btn btn-default'>Join</button></a></span>";
            $list .= "</div>";
        } // end if $num>0

        return $list;
    }

}