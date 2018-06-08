<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Quiz extends Utils
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $qid
     * @param $type
     * @return string
     */
    function get_question_answers($qid, $type)
    {
        $list = "";
        $mobile = $_SESSION['mobile'];
        if ($mobile) {
            $class = ($type == 1) ? 'poll_answers' : 'quiz_answers';
            $name = 'name_' . $qid;
            $query = "select * from mdl_poll_a where qid=$qid";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $list .= "<div style='padding-left: 10%;'>";
                $list .= "<input class='$class' name='$name' type='radio' value='" . $row['id'] . "'>&nbsp;" . $row['a'] . "";
                $list .= "</div>";
            }
        } // end if
        else {
            $list .= "<table border='0' style='475px;margin-left: 12px;'>";
            $class = ($type == 1) ? 'poll_answers' : 'quiz_answers';
            $name = 'name_' . $qid;
            $query = "select * from mdl_poll_a where qid=$qid";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $list .= "<tr>";
                $list .= "<td style='padding: 5px;text-align: left;'><input class='$class' name='$name' type='radio' value='" . $row['id'] . "'></td>";
                $list .= "<td style='padding: 5px;text-align: left;width: 400px;'>" . $row['a'] . "</td>";
                $list .= "</tr>";
            } // end while
            $list .= "</table>";
        }
        return $list;
    }

    /**
     * @param $aid
     * @param $type
     * @return string
     */
    function get_poll_data($aid, $type)
    {
        $list = "";
        $query = "select * from mdl_poll where aid=$aid and type=$type";
        $num = $this->db->numrows($query);
        $mobile = $_SESSION['mobile'];
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $pid = $row['id'];
            } // end while;
            if ($pid > 0) {
                $query = "select * from mdl_poll_q where pid=$pid";
                $num = $this->db->numrows($query);
                $result = $this->db->query($query);
                $i = 1;
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $answers = $this->get_question_answers($row['id'], $type);
                    $title = $row['title'];
                    if ($mobile) {
                        $list .= "<div style='text-align: left;'>";
                        $list .= "<input type='hidden' id='total_items_$type' value='$num'>";
                        $list .= "<div style='margin-bottom: 10px;font-weight:bold;padding-left:10%;'><br>$i)&nbsp;$title</div>";
                        $list .= "<br>$answers";
                        $list .= "</div>";
                    } // end if
                    else {
                        $list .= "<div class='row' style='text-align: left;'>";
                        $list .= "<input type='hidden' id='total_items_$type' value='$num'>";
                        $list .= "<span class='col-md-12' style='margin-bottom: 10px;font-weight:bold;'>$i)&nbsp;$title</span>";
                        $list .= $answers;
                        $list .= "</div>";
                        $list .= "<div class='row'>";
                        $list .= "<span class='col-md-12'><br></span>";
                        $list .= "</div>";
                    }
                    $i++;
                } // end while
            } // end if $pid > 0
        } // end if $num>0

        return $list;

    }

    /**
     * @param $aid
     * @param $type
     * @return int
     */
    function get_poll_id($aid, $type)
    {
        $pid = 0;
        $query = "select * from mdl_poll where aid=$aid and type=$type";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $pid = $row['id'];
            }
        } // end if $num > 0

        return $pid;
    }

    /**
     * @param $pid
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
            }
        } // end if $num>0

        return $q;
    }

    /**
     * @param $aid
     * @param $type
     * @return array
     */
    function get_poll_answers($aid, $type)
    {
        $a = array();
        $pid = $this->get_poll_id($aid, $type);
        if ($pid > 0) {
            $q = $this->get_poll_questions($pid);
            if (count($q) > 0) {
                $qs = implode(',', $q);
                $query = "select * from mdl_poll_a where qid in ($qs)";
                $num = $this->db->numrows($query);
                if ($num > 0) {
                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $a[] = $row['id'];
                    }
                } // end if $num>0
            } // end if count($q)>0
        } // end if $pid > 0

        return $a;

    }


    /**
     * @param $aid
     * @param $type
     * @param $userid
     * @return int
     */
    function is_student_already_took_poll($aid, $type, $userid)
    {
        $status = 0;
        $a = $this->get_poll_answers($aid, $type);
        if (count($a) > 0) {
            $as = implode(',', $a);
            $query = "select * from mdl_poll_student_answers where userid=$userid and aid in ($as)";
            $status = $this->db->numrows($query);
        } // end if count($a)>0

        return $status;
    }

    /**
     * @param $aid
     * @param $type
     * @param $userid
     * @return string
     */
    function get_poll_submit_btn($aid, $type, $userid)
    {
        $list = "";
        $btnID = ($type == 1) ? 'submit_poll' : 'submit_quiz';
        $btnTitle = ($type == 1) ? 'Submit Research' : 'Submit Quiz';
        $status = $this->is_student_already_took_poll($aid, $type, $userid);
        if ($status > 0) {
            $list .= "<button class='btn btn-primary' id='$btnID' disabled>$btnTitle</button><br>";
        } // end if
        else {
            $list .= "<button class='btn btn-primary' id='$btnID'>$btnTitle</button><br>";
        }

        return $list;
    }


    /**
     * @param $type
     * @return string
     */
    function get_poll_page($type)
    {
        $list = "";
        $aid = $this->get_news_id();
        if ($aid > 0) {
            $title = ($type == 1) ? 'Polling Questions' : 'News Quiz';
            $userid = $_SESSION['userid'];
            $mobile = $_SESSION['mobile'];
            $groups = $this->get_user_groups_by_userid($userid);
            if (count($groups) == 0) {
                $list .= "<br><div class='row'></div>";
            } // end if
            else {
                $data = $this->get_poll_data($aid, $type);
                $btn = $this->get_poll_submit_btn($aid, $type, $userid);
                if ($data != '') {
                    if ($mobile) {
                        $list .= "<div class='row' style='text-align: center;font-size: 35px;font-weight: bold;'>";
                        $list .= $title;
                        $list .= "</div>";
                        $list .= "<div class='row' style='text-align: center;'>";
                        $list .= "$data<br>$btn<br>";
                        $list .= "</div>";
                    } // end if
                    else {
                        $list .= "<div id='container138'>
						<div id='container127'></div>
						<div id='container137'>
							<div id='container136'>
								<div id='container128'></div>
								<div id='container135'>
									<div id='container145'>
										<br><p><span class='underline' style='margin-top: 15px;'>$title</span></p>
										<div id='container144'>
											
												<br >$data <br>
												$btn
											    <br><br>
											        
										</div>
									</div>
								</div>
							</div>
						</div>	
					</div>";
                    }
                }
            }
        }

        return $list;
    }

    /**
     * @param $data
     * @return string
     */
    function submit_quiz_results($data)
    {
        $userid = $data->userid;
        $items = $data->items;
        $now = time();
        foreach ($items as $aid) {
            $query = "insert into mdl_poll_student_answers (userid, aid, added) 
					values ($userid,$aid,'$now')";
            $this->db->query($query);
        }

        $response = ($data->type == 1) ? 'Poll is submitted successfully' : 'Quiz is submitted successfully';
        $list = "<p style='text-align: center;'>$response</p>";

        return $list;
    }


}