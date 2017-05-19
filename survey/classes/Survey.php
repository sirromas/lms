<?php

/**
 * Description of Survey
 *
 * @author moyo
 */
require_once '/homepages/17/d212585247/htdocs/globalizationplus/survey/class.database.php';
require_once 'mailer/vendor/PHPMailerAutoload.php';
require_once '/homepages/17/d212585247/htdocs/globalizationplus/lms/custom/postmark/vendor/autoload.php';

use Postmark\PostmarkClient;

class Survey {

    public $mail_smtp_host;
    public $mail_smtp_port;
    public $mail_smtp_user;
    public $mail_smtp_pwd;
    public $db;
    public $upload_path;
    public $from;
    public $subject;
    public $imp_path = '/homepages/17/d212585247/htdocs/globalizationplus/survey/img';

    function __construct() {
        $this->db = new pdo_db();
        $this->from = 'info@globalizationplus.com';
        $this->subject = 'Globalization plus - Survey';
        $this->upload_path = $_SERVER['DOCUMENT_ROOT'] . '/survey/files';
    }

    function get_config_value($config_name) {
        $query = "select * from mdl_external_survey_config "
                . "where config_name='$config_name'";
        //echo "Query: ".$query."<br>";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $value = $row['config_value'];
        }
        return $value;
    }

    function update_item_err_description($item, $error_info) {
        $query = "update mdl_external_survey_queue "
                . "set email_err='$error_info' where id=$item->id";
        $this->db->query($query);
    }

    function send_single_item($from, $recipient, $subject, $message) {
        //$client = new PostmarkClient("5a470ceb-d8d6-49cb-911c-55cbaeec199f");  // Roman
        $client = new PostmarkClient("a8a51d79-203b-43d1-b773-5de72a0b8b3a");  // Steve
        $result = $client->sendEmail($from, $recipient, $subject, $message);
        return $result;
    }

    function get_campaign_preface($item, $preview = FALSE) {
        $list = "";
        $list.="<!DOCTYPE html>";
        $list.="<html lang='en'>";
        $list.="<head>";
        $list.="<meta name='viewport' content='width=device-width, initial-scale=1'>";
        $list.="<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>";
        $list.="</head>";
        $list.="<body>";
        $list.="<div class='container-fluid'>";
        
        if ($preview) {
            $query = "select * from mdl_campaign where id=$item";
            $list.="Dear Firstname Lastname, <br>";
        } // end if 
        else {
            $query = "select * from mdl_campaign where id=$item->campid";
        } // end else
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $preface = $row['preface'];
            $search = array('{firstname}', '{lastname}');
            $replace = array($item->firstname, $item->lastname);
            $new_preface = str_replace($search, $replace, $preface);
            $list.=$new_preface;
        }

        return $list;
    }

    function get_question_answers($qid, $item, $preview) {
        $list = "";
        $clean_email = trim($item->email);
        $query = "select * from mdl_campaign_a where qid=$qid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $list.="<table border='0' style='width:100%'>";
            $list.="<tr>";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $color = $row['color'];
                if ($preview) {
                    $list.="<td style='padding:15px;'><a href='#' style='color:$color' onClick='return false' target='_blank'>" . $row['rtext'] . "</a></td>";
                } // end if
                else {
                    $query_string = "email=$clean_email&id=$id&firstname=" . urlencode($item->firstname) . "&lastname=" . urlencode($item->lastname) . "";
                    $list.="<td style='padding:15px;'><a style='color:$color' href='http://globalizationplus.com/survey/receive.php?$query_string' target='_blank'>" . $row['rtext'] . "</a></td>";
                } // end else 
            } // end while
            $list.="</tr>";
            $list.="</table>";
        } // end if $num > 0
        return $list;
    }

    function get_campaign_questions_block($item, $preview = false) {
        $list = "";
        if ($preview) {
            $query = "select * from mdl_campaign_q where campid=$item";
        } // end if
        else {
            $query = "select * from mdl_campaign_q where campid=$item->campid";
        } // end else
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $q = new stdClass();
                foreach ($row as $key => $value) {
                    $q->$key = $value;
                }
                $qs[] = $q;
            } // end while 
            $list.="<table>";
            foreach ($qs as $q) {
                $a = $this->get_question_answers($q->id, $item, $preview);
                $text = $q->qtext;
                $list.="<tr>";
                $list.="<td style='padding-left:15px;padding-right:15px;'>$text</td>";
                $list.="</tr>";

                $list.="<tr>";
                $list.="<td style='padding:35px;'>$a</td>";
                $list.="</tr>";
            } // end for
            $list.="</table>";
        } // end if $num > 0
        return $list;
    }

    function get_campaign_questions($item, $preview = false) {
        $questions = array();
        if ($preview) {
            $query = "select * from mdl_campaign_q where campid=$item";
        } // end if
        else {
            $query = "select * from mdl_campaign_q where campid=$item->campid";
        } // end else
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $qid = $row['id'];
                $answers = $this->get_question_answers2($item, $qid, $preview);
                $text = $row['qtext'];
                $q = new stdClass();
                $q->t = $text;
                $q->a = $answers;
                $questions[] = $q;
            } // end while
        } // end if $num > 0
        return $questions;
    }

    function get_question_answers2($item, $qid, $preview) {
        $query = "select * from mdl_campaign_a where qid=$qid order by id desc";
        $clean_email = trim($item->email);
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $list = "";
                $id = $row['id'];
                $color = $row['color'];
                $img = $row['img'];
                if ($preview) {
                    if ($img == null) {
                        $list.="<a href='#' style='color:$color' onClick='return false' target='_blank'>" . $row['rtext'] . "</a>";
                    } // end if $img==null
                    else {
                        $img_path = 'http://' . $_SERVER['SERVER_NAME'] . '/survey/img/' . $img;
                        $list.="<a href='#' onClick='return false' target='_blank'><img src='$img_path' style='cursor:pointer;'></a>";
                    } // end else
                } // end if preview
                else {
                    $query_string = "email=$clean_email&id=$id&firstname=" . urlencode($item->firstname) . "&lastname=" . urlencode($item->lastname) . "";
                    if ($img == null) {
                        $list.="<a style='color:$color' href='http://globalizationplus.com/survey/receive.php?$query_string' target='_blank'>" . $row['rtext'] . "</a>";
                    } // end if
                    else {
                        $img_path = 'http://' . $_SERVER['SERVER_NAME'] . '/survey/img/' . $img;
                        $list.="<a style='color:$color' href='http://globalizationplus.com/survey/receive.php?$query_string' target='_blank'><img src='$img_path' style='cursor:pointer;'></a>";
                    } // end else
                } // end else 
                $answers[] = $list;
            } // end while
        } // end if $num > 0
        return $answers;
    }

    function get_campaign_answers($item, $preview) {
        if ($preview) {
            $query = "select * from mdl_campaign_q where campid=$item";
        } // end if $preview
        else {
            $query = "select * from mdl_campaign_q where campid=$item->campid";
            $clean_email = trim($item->email);
        } // end else
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $qid[] = $row['id'];
        } // end while
        foreach ($qid as $id) {
            $query = "select * from mdl_campaign_a where qid=$id order by id";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $list = "";
                    $id = $row['id'];
                    $color = $row['color'];
                    $img = $row['img'];
                    if ($preview) {
                        if ($img == null) {
                            $list.="<a href='#' style='color:$color' onClick='return false' target='_blank'>" . $row['rtext'] . "</a>";
                        } // end if $img==null
                        else {
                            $img_path = 'http://' . $_SERVER['SERVER_NAME'] . '/survey/img/' . $img;
                            $list.="<a href='#' onClick='return false' target='_blank'><img src='$img_path' style='cursor:pointer;'></a>";
                        } // end else
                    } // end if preview
                    else {
                        $query_string = "email=$clean_email&id=$id&firstname=" . urlencode($item->firstname) . "&lastname=" . urlencode($item->lastname) . "";
                        if ($img == null) {
                            $list.="<a style='color:$color' href='http://globalizationplus.com/survey/receive.php?$query_string' target='_blank'>" . $row['rtext'] . "</a>";
                        } // end if
                        else {
                            $img_path = 'http://' . $_SERVER['SERVER_NAME'] . '/survey/img/' . $img;
                            $list.="<a style='color:$color' href='http://globalizationplus.com/survey/receive.php?$query_string' target='_blank'><img src='$img_path' style='cursor:pointer;'></a>";
                        } // end else
                    } // end else 
                    $answers[] = $list;
                } // end while
            } // end if $num > 0
        } // end foreach

        return $answers;
    }

    function get_specific_questions_answers() {
        
    }

    function get_question_answers_for_replace($total_answers, $i) {
        for ($k = 1; $k <= $total_answers; $k++) {
            $a_item = "{q" . $i . "_a" . $k . "}";
            $a_search[] = $a_item;
        } // end for
        return $a_search;
    }

    function replace_questions_text($preface, $questions) {
        $qa = [];
        $i = 1;
        foreach ($questions as $q) {
            $t = $q->t;
            $q_search[] = "{q$i}";
            $qa[] = $t;
            $i++;
        } // end foreach
        $msg = str_replace($q_search, $qa, $preface);
        return $msg;
    }

    function replace_question_answers($subject, $a_search, $answers) {
        $msg = str_replace($a_search, $answers, $subject);
        return $msg;
    }

    function create_message($item, $preface, $questions, $preview) {
        $msg = "";
        $i = 1;
        $message_with_questions = $this->replace_questions_text($preface, $questions);
        $answers = $this->get_campaign_answers($item, $preview);
        foreach ($questions as $q) {
            $a = $q->a;
            $total_answers = count($a);
            for ($k = 1; $k <= $total_answers; $k++) {
                $a_item = "{q" . $i . "_a" . $k . "}";
                $a_search[] = $a_item;
            } // end for
            $i++;
        } // end foreach

        $msg.=str_replace($a_search, $answers, $message_with_questions);
        $msg.="</div>";
        $msg.="</body>";
        $msg.="</html>";
        return $msg;
    }

    function prepare_campaign_answers($answers, $item, $preview) {
        $new_answers = "";
        if ($preview) {
            $query = "select * from mdl_campaign where id=$item";
        } // end if 
        else {
            $query = "select * from mdl_campaign where id=$item->campid";
        } // end else
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $preface = $row['preface'];
        }
        $total = count($answers);
        if ($total > 0) {
            for ($i = 1; $i <= $total; $i++) {
                $a_item = "{a$i}";
                $search[] = $a_item;
            } // end for

            $new_answers = str_replace($search, $answers, $preface);
        } // end if $total>0
        return $new_answers;
    }

    function compose_message($item, $preview = false) {
        $list = "";
        $preface = $this->get_campaign_preface($item, $preview);
        $questions = $this->get_campaign_questions($item, $preview);
        $campaign = $this->create_message($item, $preface, $questions, $preview);

        $list.="<table>";

        $list.="<tr>";
        $list.="<td style=''>$campaign</td>";
        $list.="</tr>";

        $list.="</table>";
        return $list;
    }

    function preview_campaign($id) {
        $list = "";
        $camp = $this->compose_message($id, true);

        $list.="<div class='row'>";
        $list.="<div class='col-4'><button class='btn btn-default' id='back_camp'>Back</button><br><br></div>";
        $list.="</div>";

        $list.="<div class='row'>";
        $list.="<div class='col-4'>$camp</div>";
        $list.="</div>";

        return $list;
    }

    function get_campaign_detailes($item) {
        $query = "select * from mdl_campaign where id=$item->campid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $camp = new stdClass();
            foreach ($row as $key => $value) {
                $camp->$key = $value;
            } // end foreach
        } // end while
        return $camp;
    }

    function send_survey_email($item) {
        $list = "";
        $email = $item->email;
        $campdata = $this->get_campaign_detailes($item);
        $from = $campdata->from_email;
        $subject = $campdata->subject;
        $message = $this->compose_message($item);
        $status = $this->send_single_item($from, $email, $subject, $message);
        if ($status) {
            $list.="Email was successfully sent";
        } // end if
        else {
            $list.="Email was not sent (failure)";
        } // end else
        return $list;
    }

    function send_survey_results($item) {
        $list = "";
        $id = $item['id'];
        $email = $item['email'];
        $firstname = urldecode($item['firstname']);
        $lastname = urldecode($item['lastname']);
        $date = time();
        $query = "insert into mdl_campaign_r (firstname, lastname, email,rid,added) "
                . "values('$firstname','$lastname', '$email','$id','$date')";
        $this->db->query($query);

        $list.="<p style='text-align:center;'>"
                . "<img class='dsR1145' src='http://globalizationplus.com/assets/images/header.jpg' style='padding-top: 4px; border-style: solid; border-width: 6px 1px 2px; border-color: #ddd;' alt='' usemap='#header' border='0'><map name='header' id='header'>"
                . "<area title='About Us' shape='rect' coords='609,23,683,40' href='http://globalizationplus.com/about.html' alt='About Us' target='_blank'>"
                . "<area title='Globalization Plus' shape='rect' coords='120,55,556,102' href='http://globalizationplus.com' alt='Globalizaiton Plus' target='_blank'><area title='Log-In' shape='rect' coords='17,19,68,39' href='http://globalizationplus.com/login.html' alt='Log-In' target='_blank'></map></p>";

        $list.= "<br><div style='margin:auto;text-align:center;font-weight:bold;font-size:25px;'>Thank you very much!</div>";
        return $list;
    }

    function check_user($username, $password) {
        $query = "select * from mdl_external_survey_config "
                . "where config_name='username'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $db_username = $row['config_value'];
        }

        $query = "select * from mdl_external_survey_config "
                . "where config_name='password'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $db_password = $row['config_value'];
        }

        if ($db_username == $username && $db_password == $password) {
            return true;
        } // end if $db_username==$username && $db_password==$password
        else {
            return false;
        } // end else
    }

    function get_config_inout_id($config_name) {

        switch ($config_name) {
            case 'smtp_host':
                echo "i равно 0";
                break;
            case '':
                echo "i равно 1";
                break;
            case '':
                echo "i равно 2";
                break;
            case '':
                echo "i равно 2";
                break;
            case '':
                echo "i равно 2";
                break;
        }
    }

    function get_questions_drop_down() {
        $list = "";
        $list.="<select id='camp_q_num'>";
        $list.="<option value='0' selected>Please select survey questions number</option>";
        for ($i = 1; $i <= 10; $i++) {
            $list.="<option value='$i'>$i</option>";
        }
        $list.="</select>";
        return $list;
    }

    function get_campaign_page() {
        $list = "";
        $qbox = $this->get_questions_drop_down();
        $camps = $this->create_camp_list();

        $list.="<div class='row-fluid' style='padding-bottom:15px;'>";
        $list.="<span class='col-sm-1' style='padding-left:0px;'>From*</span>";
        $list.="<span class='span6'><input type='text' style='width:897px;' id='from'></span>";
        $list.="</div>";

        $list.="<div class='row-fluid' style='padding-bottom:15px;'>";
        $list.="<span class='col-sm-1' style='padding-left:0px;'>Subject*</span>";
        $list.="<span class='span6'><input type='text' style='width:897px;' id='subject'></span>";
        $list.="</div>";

        $list.="<div class='row-fluid' style='padding-bottom:15px;'>";
        $list.="<span class='col-sm-1' style='padding-left:0px;'>Title*</span>";
        $list.="<span class='span6'><input type='text' style='width:897px;' id='camp_title'></span>";
        $list.="</div>";

        $list.="<div class='row-fluid'>";

        $list.="<span class='span12'>";
        $list.="<textarea name='editor1' id='editor1' rows='10' style='width:675px;'>";
        $list.="</textarea>
            <script>
                CKEDITOR.replace( 'editor1' );
            </script>";
        $list.="</span>";

        $list.="</div>";

        $list.="<div class='row-fluid'>";
        $list.="<span class='col-sm-1' style='padding-left:0px;'><br>$qbox</span>";
        $list.="</div><br>";

        $list.="<br><div class='row-fluid'>";
        $list.="<span class='col-sm-12' style='padding-left:0px;' id='q_container'></span>";
        $list.="</div>";

        /*
          $list.="<br><div id='q_container'>";
          $list.=$questions;
          $list.="</div>";
         */

        $list.="<div class='row'>";
        $list.="<span class='col-sm-12' id='camp_err' style='padding-top:15px;color:red;'></span>";
        $list.="</div>";

        $list.="<div class='row' style='padding-top:15px;padding-left:15px;display:none;' id='button_container'>";
        $list.="<span class='col-sm-1' style='padding-left:0px;'><button class='btn btn-default' id='add_camp'>Add</button></span>";
        $list.="</div><br>";

        $list.="<div class='row' style='padding-top:15px;'>";
        $list.="<span class='col-sm-12'>$camps</span>";
        $list.="</div>";


        return $list;
    }

    function delete_question_answers($ids) {
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $query = "delete from mdl_campaign_a where qid=$id";
                $this->db->query($query);
            } // end foreach
        } // count($ids)>0
    }

    function delete_questions($campid) {
        $query = "select * from mdl_campaign_q where campid=$campid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $ids[] = $row['id'];
            } // end while
            $this->delete_question_answers($ids);
        } // end if $num > 0

        $query = "delete from mdl_campaign_q where campid=$campid";
        $this->db->query($query);
    }

    function del_campaign($id) {
        $this->delete_questions($id);
        $query = "delete from mdl_campaign where id=$id";
        $this->db->query($query);
    }

    function get_reply_grade_box($i) {
        $list = "";

        $list.="<select id='q_grade_$i'>";

        $list.="</select>";
        return $list;
    }

    function get_question_replies_block($i) {
        $list = "";
        for ($k = 1; $k <= 6; $k++) {
            $list.="<div class='row'>";
            $pickerid = '#cpicker_' . $k . '_' . $i;
            $elid = 'cpicker_' . $k . '_' . $i;
            $list.="<span class='col-sm-2'>Choice #$k</span>";
            $list.="<span class='col-sm-7'><input type='text' id='r_" . $k . "_" . $i . "' style='width:575px;'></span>";
            $list.="<span class='col-sm-1'><input id='$elid' type='text' class='form-control' style='width:80px;'/ placeholder='Color'>
                    <script>
                        $(function() {
                            $('$pickerid').colorpicker();
                        });
                    </script></span>";
            $list.="</div>";
        }
        return $list;
    }

    function get_questions_block($num) {
        $list = "";
        $list.="<input type='hidden' id='q_num' value='$num'>";
        for ($i = 1; $i <= $num; $i++) {
            $q = $this->get_question_replies_block($i);
            $list.="<br><div class='row'>";
            $list.="<span class='col-sm-2'>Question* </span>";
            $list.="<span class='col-sm-6'><input type='text' id='q_text_$i' style='width:658px;'></span>";
            $list.="</div>";

            $list.="<div class='row'>";
            $list.="<span class='col-sm-12'><br>$q</span>";
            $list.="</div>";

            $list.="<div class='row'>";
            $list.="<span class='col-sm-12'><hr></span>";
            $list.="</div>";
        }
        return $list;
    }

    function create_camp_list() {
        $list = "";

        $query = "select * from mdl_campaign order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $c = new stdClass();
                foreach ($row as $key => $value) {
                    $c->$key = $value;
                } // end foreach
                $camps[] = $c;
            } // end while
        } // end if $num > 0

        if (count($camps) > 0) {
            $list.="<div id='camp_container'>";
            $list.="<table id='camps' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

            $list.="<thead>";
            $list.="<tr>";
            $list.="<th style='padding:15px;'>Title</th>";
            $list.="<th style='padding:15px;'>Content</th>";
            $list.="<th style='padding:15px;'>Added</th>";
            $list.="<th style='padding:15px;'>Ops</th>";
            $list.="</tr>";
            $list.="</thead>";

            $list.="<tbody>";
            foreach ($camps as $c) {
                $date = date('m-d-Y', $c->added);
                $list.="<tr>";
                $list.="<td style='padding:15px'>$c->title</td>";
                $list.="<td style='padding:15px' width='65%'>$c->preface</td>";
                $list.="<td style='padding:15px;'>$date</td>";
                $list.="<td style='padding:15px;'>"
                        . "<span title='Edit' class='glyphicon glyphicon-wrench' id='camp_edit_$c->id' style='cursor:pointer;'></span>&nbsp;&nbsp;"
                        . "<span title='Delete' class='glyphicon glyphicon-trash' id='camp_del_$c->id' style='cursor:pointer;'></span>&nbsp;&nbsp;"
                        . "<span title='Preview' class='glyphicon glyphicon-eye-open' id='camp_preview_$c->id' style='cursor:pointer;'></span></td>";
                $list.="</tr>";
            } // end foreach
            $list.="</tbody>";

            $list.="</table>";
            $list.="</div>";
        } // end if count($camps) > 0
        else {
            $list.="<div class='row-fluid'>";
            $list.="<span class='span9'>There are no any campaign added </span>";
            $list.="</div>";
        } // end else

        return $list;
    }

    function get_table_last_record_id($table) {
        $query = "select * from $table order by id desc limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
        }
        return $id;
    }

    function add_camp($camp) {
        /*
          echo "<pre>";
          print_r($camp);
          echo "</pre>";
          die();
         */

        $title = $camp->title;
        $preface = $camp->content;
        $clear_peface = str_replace("'", "\'", $preface);
        $from = $camp->from;
        $subject = $camp->subject;
        $questions = $camp->questions;
        $date = time();
        $query = "insert into mdl_campaign "
                . "(from_email,subject,title,preface,added) "
                . "values ('$from', '$subject', '$title', '$clear_peface','$date')";
        $this->db->query($query);
        $campLastId = $this->get_table_last_record_id('mdl_campaign');
        foreach ($questions as $q) {
            $text = $q->qtext;
            $clear_text = str_replace("'", "\'", $text);
            $replies = $q->replies; // array
            $query = "insert into mdl_campaign_q (campid,qtext) "
                    . "values($campLastId,'$clear_text')";
            $this->db->query($query);
            $lastqID = $this->get_table_last_record_id('mdl_campaign_q');
            foreach ($replies as $a) {
                $clear_a = str_replace("'", "\'", $a->text);
                $color = $a->color;
                $query = "insert into mdl_campaign_a (qid, rtext, color) "
                        . "values($lastqID,'$clear_a', '$color')";
                $this->db->query($query);
            } // end foreach
        } // end foreach
        return true;
    }

    function get_settings_page() {
        $list = "";
        $configs = array();
        $query = "select * from mdl_external_survey_config";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $configs[] = $row;
        }

        $list.="<table border='0' align='left' style='padding-left:35px;'>";
        foreach ($configs as $config_item) {
            $list.="<tr>";
            $list.="<td>" . $config_item['config_name'] . "</td><td style='padding:15px;'><input type='text' value='" . $config_item['config_value'] . "' id='" . $config_item['config_name'] . "' style='width:375px;'></td>";
            $list.="</tr>";
        }

        $list.="<tr>";
        $list.="<td></td><td style='padding:15px;'><span id='config_err'></span></td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style=''><button class='btn btn-default' id='update_config'>Submit</button></td>";
        $list.="</tr>";
        $list.="";
        $list.="</table>";

        return $list;
    }

    function update_config($configObj) {
        foreach ($configObj as $key => $value) {
            $query = "update mdl_external_survey_config "
                    . "set config_value='$value' where config_name='$key'";
            $this->db->query($query);
        }
        $list = "Config data are updated";
        return $list;
    }

    function put_item_into_queue($item, $campid) {
        $date = time();
        $sent = 0; // temp workaround
        $query = "insert into mdl_campaign_queue "
                . "(firstname, lastname, email, campid, sent,  added) "
                . "values ('$item[0]', '$item[1]', '$item[2]','$campid','$sent', '$date')";
        $this->db->query($query);
    }

    function upload_emails_list($files, $post) {
        $list = "";
        $campid = $post['campid'];
        $file = $files[0];
        if ($file['error'] == 0 && $file['size'] > 0) {
            $filename = time() . rand(10, 175);
            $full_file_path = $this->upload_path . '/' . $filename . '.csv';
            if (move_uploaded_file($file['tmp_name'], $full_file_path)) {
                $csv_data = array_map('str_getcsv', file($full_file_path));
                if (count($csv_data) > 0) {
                    foreach ($csv_data as $item) {
                        if ($item[0] != '' && $item[1] != '' && $item[2] != '') {
                            $this->put_item_into_queue($item, $campid);
                        } // end if $item[0]!='' && $item[1]!='' && $item[2]!=''
                    } // end foreach
                    $list.="Recipients list is put into queue and will be sent soon.";
                } // end if count($csv_data) > 0
                else {
                    $list.="No data found";
                }
            } // end if move_uploaded_file($file['tmp_name'], $full_file_path)
            else {
                $list.="Error uploading file (move uploaded file)";
            }
        } // end if $files['error']==0
        else {
            $list.="Error uploading file";
        }
        return $list;
    }

    function process_queueu_items() {
        $query = "select * from mdl_campaign_queue "
                . "where sent=0 order by added desc limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            $item->firstname = $row['firstname'];
            $item->lastname = $row['lastname'];
            $item->id = $row['id'];
            $item->email = $row['email'];
            $item->campid = $row['campid'];
            $status = $this->send_survey_email($item);
            if ($status) {
                $query = "update mdl_campaign_queue set sent=1 where id=$item->id";
            } // end if $status
            else {
                $query = "update mdl_campaign_queue set sent=-1 where id=$item->id";
            } //end else
            $this->db->query($query);
        }
    }

    function get_res_ampaigns_list() {
        $list = "";
        $list.="<select id='res_campaigns_list' style='width:130px;'>";
        $list.="<option value='0' selected>Please select</option>";
        $query = "select * from mdl_campaign order by preface";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $item = $row['title'];
                $list.="<option value='$id'>$item</option>";
            } // end while
        } // end if $num > 0
        $list.="</select>";
        return $list;
    }

    function process_question_data($rid) {
        $query = "select count(id) as total from mdl_campaign_r where rid=$rid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $total = $row['total'];
        }
        return $total;
    }

    function get_campaign_question_stat($qid, $name) {
        $list = "";
        $polls = array();
        $query = "select * from mdl_campaign_a where qid=$qid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $answers[] = $row['id'];
            }
            $alist = implode(',', $answers);
            $query = "select * from mdl_campaign_r "
                    . "where rid in ($alist) group by rid";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $p = new stdClass();
                    $rid = $row['rid'];
                    $stat = $this->process_question_data($rid);
                    $p->rid = $rid;
                    $p->stat = $stat;
                    $polls[] = $p;
                } // end while
            } // end if $num > 0
        } // end if $num > 0

        if (count($polls) > 0) {
            $list.="<table id='res_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            $list.="<thead>";
            $list.="<tr>";
            $list.="<th>Question text</th>";
            $list.="<th>Hits</th>";
            $list.="</tr>";
            $list.="</thead>";
            $list.="<tbody>";
            foreach ($polls as $p) {
                $name = $this->get_reply_text($p->rid);
                $list.="<tr>";
                $list.="<td>$name</td>";
                $list.="<td>$p->stat</td>";
                $list.="</tr>";
            } // end foreach
            $list.="</tbody>";
            $list.="</table>";

            $link = $this->get_campaign_csv_file($qid);
            $list.="<div class='row'>";
            $list.="<span calss='col-md-4'>$link</span>";
            $list.="</div>";
        } // end if count($polls)>0
        else {
            $list.="<div class='row'>";
            $list.="<span calss='col-md-4' style='padding-left:29px;'>No one replied to survey yet</span>";
            $list.="</div>";
        }
        return $list;
    }

    function get_campaign_csv_file($qid) {
        $list = "";
        $filename = 'users.csv';
        $total = $this->create_csv_file($filename, $qid);
        $list.="<span class='col-md-3'><a href='http://" . $_SERVER['SERVER_NAME'] . "/survey/files/$filename' target='_blank'>Download</a></span>";
        return $list;
    }

    function get_reply_text($rid) {
        $query = "select * from mdl_campaign_a where id=$rid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['rtext'];
        }
        return $name;
    }

    function get_question_result_data($qid) {
        $list = "";
        $query = "select * from mdl_campaign_q where id=$qid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $qid = $row['id'];
            $name = $row['qtext'];
            $stat = $this->get_campaign_question_stat($qid, $name);

            $list.="<div class='row'>";
            $list.="<span class='col-md-9' style='padding-left:15px;padding-top:25px;font-weight:bold;'>$name</span>";
            $list.="<div>";

            $list.="<div class='row'>";
            $list.="<span class='col-md-4' style='padding-left:15px;padding-top:25px;'>$stat</span>";
            $list.="<span class='col-md-8' id='q_chart_$qid' style='padding-left:15px;padding-top:25px;padding-right:15px;'></span>";
            $list.="<div>";
        } // end while
        $chartstat = $this->get_campaign_question_chart_stat($qid);
        $data = new stdClass();
        $data->qid = $qid;
        $data->table = $list;
        $data->stat = $chartstat;
        return $data;
    }

    function get_campaign_results($campid) {
        $query = "select * from mdl_campaign_q where campid=$campid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $this->get_question_result_data($row['id']);
        } // end while
        return json_encode($data);
    }

    function get_results_page() {
        $list = "";
        $campaignslist = $this->get_res_ampaigns_list();
        $list.="<div class='row'>";
        $list.="<span class='col-md-12'>$campaignslist</span>";
        $list.="</div>";

        $list.="<div class='row'>";
        $list.="<span class='col-md-12' id='res_loader' style='padding-left:50px;padding-top:20px;display:none;'><img src='http://globalizationplus.com/assets/images/ajax.gif'></span>";
        $list.="</div>";

        return $list;
    }

    function get_chart_data($campid) {
        $query = "select * from mdl_campaign_q where campid=$campid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $qid = $row['id'];
                $stat = $this->get_campaign_question_chart_stat($qid);
            } // end while
            return $stat;
        } // end if $num > 0
    }

    function get_campaign_question_chart_stat($qid) {
        $polls = array();
        $query = "select * from mdl_campaign_a where qid=$qid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $answers[] = $row['id'];
            }
            $alist = implode(',', $answers);
            $query = "select * from mdl_campaign_r "
                    . "where rid in ($alist) group by rid";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $rid = $row['rid'];
                    $stat = $this->process_question_data($rid);
                    $name = $this->get_reply_text($rid);
                    $p = $name . '@' . $stat;
                    $polls[] = $p;
                } // end while
            } // end if $num > 0
        } // end if $num > 0
        return json_encode($polls);
    }

    function get_poll_results() {
        $counter_20 = 0;
        $counter_50 = 0;
        $counter_80 = 0;
        $counter_100 = 0;
        $query = "select * from mdl_external_survey_result";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $db_result = $row['poll_result'];
            switch ($db_result) {
                case 20:
                    $counter_20++;
                    break;
                case 50:
                    $counter_50++;
                    break;
                case 80:
                    $counter_80++;
                    break;
                case 100:
                    $counter_100++;
                    break;
            }
        }
        $resultObj = new stdClass();
        $resultObj->p20 = $counter_20;
        $resultObj->p50 = $counter_50;
        $resultObj->p80 = $counter_80;
        $resultObj->p100 = $counter_100;
        return $resultObj;
    }

    function get_queue_status() {
        $list = '';
        $responders = $this->get_responders_data();
        $query = "select count(id) as total "
                . "from mdl_external_survey_queue where sent=0";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $not_sent = $row['total'];
        }

        $query = "select count(id) as total "
                . "from mdl_external_survey_queue where sent=1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $sent = $row['total'];
        }

        $query = "select count(id) as total "
                . "from mdl_external_survey_queue where sent=-1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $failed = $row['total'];
        }

        $total = $not_sent + $sent + $failed;

        $list.="<table border='0'>";

        $list.="<tr>";
        $list.="<th style='padding:15px'>Not sent</th>";
        $list.="<th style='padding:15px'>Success</th>";
        $list.="<th style='padding:15px'>Failed</th>";
        $list.="<th style='padding:15px'>Total</th>";
        $list.="<th style='padding:15px'>&nbsp;</th>";
        $list.="<th style='padding:15px'>Responders</th>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px'>$not_sent</td>";
        $list.="<td style='padding:15px'>$sent</td>";
        $list.="<td style='padding:15px'>$failed</td>";
        $list.="<td style='padding:15px'>$total</td>";
        $list.="<td style='padding:15px'>&nbsp;</td>";
        $list.="<td style='padding:15px'>$responders</td>";
        $list.="</tr>";

        $list.="</table>";


        return $list;
    }

    function get_responders_data() {
        $list = '';
        $query = "select count(id) as total from mdl_campaign_r";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $sent = $row['total'];
        }
        $filename = 'users.csv';
        $this->create_csv_file($filename);
        $list.="<div class='row'>";
        $list.="<span style='font-weight:bold;'>Total items:  $sent</span><span style='padding-left:8px;'><a href='http://globalizationplus.com/survey/files/users.csv' target='_blank'>Download</a></span>";
        $list.="</div>";
        return $list;
    }

    function get_answer_name($rid) {
        $query = "select * from mdl_campaign_a where id=$rid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['rtext'];
        }
        return $name;
    }

    function create_csv_file($filename, $qid) {

        $query = "select * from mdl_campaign_a where qid=$qid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $aids[] = $row['id'];
        }
        $alist = implode(',', $aids);

        // Write CSV data
        $path = $this->upload_path . '/' . $filename;
        $output = fopen($path, 'w');
        fputcsv($output, array('User Firstname', 'User Lastname', 'User Email', 'Hit', 'Date'));
        $query = "select * from mdl_campaign_r where rid in ($alist) order by added ";
        $total = $this->db->numrows($query);
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $this->get_answer_name($row['rid']);
            $date = date('m-d-Y', $row['added']);
            fputcsv($output, array($row['firstname'], $row['lastname'], $row['email'], $name, $date));
        }
        fclose($output);
        return $total;
    }

    function get_campaigns_list() {
        $list = "";
        $list.="<select id='campaigns_list' style='width:235px;'>";
        $list.="<option value='0' selected>Please select</option>";
        $query = "select * from mdl_campaign order by added desc";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $list.="<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
            } // end while
        } // end if $num > 0
        $list.="</select>";
        return $list;
    }

    function get_send_email_page() {
        $list = "";
        $campaign = $this->get_campaigns_list();

        $list.="<br><br><div class='row-fluid' style='padding-left: 35px;padding-top: 15px;'>";
        $list.="<span class='span12'>$campaign</span>";
        $list.="</div>";

        $list.="<div class='row-fluid' style='padding-left: 35px;padding-top: 15px;'>  
                    <span class='span12'>
                        <form action='launch.php' method='post' id='launcher' name='launcher'>
                            <div class='form-group'>
                                <label for='fname'>Firstname</label>
                                <input type='text'  style='width: 235px;' class='form-control' id='fname' name='fname' placeholder='Enter First Namee'>
                            </div>
                            <div class='form-group'>
                                <label for='lname'>Lastname</label>
                                <input type='text'  style='width: 235px;' class='form-control' id='lname' name='lname' placeholder='Enter Last Name'>
                            </div>
                            <div class='form-group'>
                                <label for='email'></label>
                                <input type='email'  style='width: 235px;' class='form-control' id='email' name='email' placeholder='Enter Email Address'>
                            </div>
                            <div class='form-group'>
                                <label class='control-label'>Or select CSV file to be uploaded:</label>
                                <input id='file' name='file' type='file' class='file'>
                            </div>
                            <div class='form-group'>
                                <span id='form_err'></span>
                            </div>    
                            <button type='submit' class='btn btn-default'>Submit</button>
                        </form>
                    </span>
                </div>";

        return $list;
    }

    function get_question_edit_answers($qid) {
        $list = "";
        $i = 1;
        $query = "select * from mdl_campaign_a where qid=$qid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $list.="<input type='hidden' id='a_num' value='$num'>";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $a = $row['rtext'];
                $m = $row['img'];
                $list.="<div clas='row'>";
                if ($m == '') {
                    $list.="<span style='margin-left:15px;'>Choice #$i&nbsp;&nbsp;<input type='text' class='q_a_$qid' data-id='$id' value='$a' style='width:375px;'></span>";
                } // end if
                else {
                    $path = "http://" . $_SERVER['SERVER_NAME'] . "/survey/img/$m";
                    $list.="<span style='margin-left:15px;'>Choice #$i&nbsp;&nbsp;<img src='$path'></span>";
                    $list.="<span style='margin-left:15px;'><button class='btn btn-default' id='del_img_$id'>Delete Image</button></span>";
                } // end else
                $list.="<span style='margin-left:15px;'><label class='btn btn-default btn-file'>Browse <input type='file' id='a_img_$id' style='display: none;'></label></span>";
                $list.="<span style='margin-left:15px;'><button class='btn btn-default' id='upload_img_$id'>Upload Image</button></span>";
                $list.="<span style='margin-left:15px;' id='upload_msg_$id' style='width:100px;'>&nbsp;</span>";

                $list.="</div>";
                $i++;
            } // end while
        } // end if $num > 0
        return $list;
    }

    function get_survey_questions_block($qs) {
        $list = "";
        $i = 1;
        if (count($qs) > 0) {
            $num = count($qs);
            $list.="<input type='hidden' id='q_num' value='$num'>";
            foreach ($qs as $q) {
                $a = $this->get_question_edit_answers($q->id);
                $list.="<br><div class='row'>";
                $list.="<span style='margin-left:15px;'><textarea id='q_edit_$i' data-id='$q->id' style='width:1004px;' rows='5'>$q->qtext</textarea>";
                $list.="</div>";

                $list.="<div class='row'>";
                $list.="<span class=''>$a</span>";
                $list.="</div>";

                $list.="<div class='row'>";
                $list.="<span class=''><br/></span>";
                $list.="</div>";
                $i++;
            } // end foreach
        } // end if count($qs

        return $list;
    }

    function get_edit_question_preface($id) {
        $query = "select * from mdl_campaign where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $email_text = $row['preface'];
        }
        return $email_text;
    }

    function get_survey_edit_page($id) {
        $list = "";

        $list.="<div>";
        $list.="<span><button class='btn btn-default' id='back_camp'>Back</button><br><br></span>";
        $list.="</div>";

        $email_text = $this->get_edit_question_preface($id);
        $query = "select * from mdl_campaign_q where campid=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $q = new stdClass();
            foreach ($row as $key => $value) {
                $q->$key = $value;
            }
            $q->a = $this->get_question_answers($q->id);
            $qs[] = $q;
        }

        $questions = $this->get_survey_questions_block($qs);
        $list.="<input type='hidden' id='campid' value='$id'>";
        $list.="<div class='row'>";
        $list.="<textarea name='editor1' id='editor1' rows='10' style='width:675px;'>$email_text</textarea>";
        $list.="<script>
                CKEDITOR.replace( 'editor1' );
            </script>";
        $list.="</div>";

        $list.="<div class='row'>";
        $list.="<span class='col-12' style='margin-left:5px;'>$questions</span>";
        $list.="</div>";

        $list.="<div class='row'>";
        $list.="<span class='col-12' style='margin-left:5px;' id='camp_err' style='color:red;'></span>";
        $list.="</div>";

        $list.="<div class='row'>";
        $list.="<span class='col-12' style='margin-left:5px;'><button class='btn btn-default' id='update_camp'>Update</button></span>";
        $list.="</div><br>";

        return $list;
    }

    function update_camp($camp) {
        $campid = $camp->id;
        $preface = $camp->msg;
        $clear_peface = str_replace("'", "\'", $preface);

        $query = "update mdl_campaign set preface='$clear_peface' "
                . "where id=$campid";
        $this->db->query($query);

        $questions = json_decode($camp->q);
        if (count($questions) > 0) {
            foreach ($questions as $q) {
                $qid = $q->id;
                $qtext = $q->text;
                $clear_text = str_replace("'", "\'", $qtext);
                $query = "update mdl_campaign_q set qtext='$clear_text' "
                        . "where id=$qid";
                $this->db->query($query);

                $answers = $q->a;
                if (count($answers) > 0) {
                    foreach ($answers as $a) {
                        $aid = $a->aid;
                        $atext = $a->text;
                        $clear_text = str_replace("'", "\'", $atext);
                        $query = "update mdl_campaign_a set rtext='$clear_text' "
                                . "where id=$aid";
                        $this->db->query($query);
                    } // end foreach
                } // end if count($answers)>0
            } // end foreach
        } // end if count($questions
    }

    function upload_link_image($files, $post) {
        $list = "";
        $file = $files[0];
        $tmp_name = $file['tmp_name'];
        $error = $file['error'];
        $size = $file['size'];
        $id = $post['a_id'];
        if ($tmp_name != '' && $size > 0 && $error == 0) {
            $filename = time();
            $fullpath = $this->imp_path . '/' . $filename;
            $status = move_uploaded_file($tmp_name, $fullpath);
            if ($status) {
                $query = "update mdl_campaign_a set img='$filename' where id=$id";
                $this->db->query($query);
                $list.="File has been uploaded";
            } // end if status
            else {
                $list.="Error uploading file";
            } // end else
        } // end if $tmp_name != ''
        else {
            $list.="Please select file to upload";
        } // end else
        return $list;
    }

    function del_answer_image($id) {
        $query = "update mdl_campaign_a set img='' where id=$id";
        $this->db->query($query);
    }

}
