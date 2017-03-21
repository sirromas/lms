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

    function __construct() {
        $this->db = new pdo_db();

        /*
          $this->mail_smtp_host = $this->get_config_value('smtp_host');
          $this->mail_smtp_port = $this->get_config_value('smtp_port');
          $this->mail_smtp_user = $this->get_config_value('smtp_user');
          $this->mail_smtp_pwd = $this->get_config_value('smtp_password');
         */


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

    function create_survey_email($email) {

        $msg = "";

        $msg.="<!DOCTYPE html>

        <html>
            <head>
                <title>Survey</title>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width = device-width, initial-scale = 1.0'>

            </head>
        <body>
        <br>
        <div style='text-align: center;margin: auto;' class='main'>

        <input type='hidden' id='email' value='$email'>

        <div style='width:800px;text-align: center;margin: auto;'>
            <p align='left' style='font-weight: bold;'>Dear Colleague, </p><br>
            <p align='justify'>I am a Professor of Advanced Writing for 
                International Relations & Global Economics at the 
                University of Southern California 
                (<a href='http://dornsife.usc.edu/cf/faculty-and-staff/faculty.cfm?pid=1027494' target='_blank'>you may click here for my USC faculty bio</a>) 
                and have a quick question regarding my research on 
                current events awareness among undergraduate 
                political science majors.<br><br>

                <span>
                    Your answer to the following would be deeply appreciated as
                    it would greatly benefit my research:
                </span>

                <br><br>
                <span class='tab'>When speculating about the news literacy of students, how many
                    of your incoming undergraduates will know that Turkey's 
                    President Recep Tayyip Erdoğan met with Vladimir Putin in 
                    St. Petersburg to restore ties that were frayed when Turkey 
                    downed a Russian jet operating along the Turkey-Syrian border? 
                </span>

            </p>
        </div>

        <div style='width:800px;text-align: center;margin:auto;'>

            <table align='center' border='0'>

                <tr>
                    
                    <!-- 
                    <td style='padding: 35px;'><img src='http://globalizationplus.com/survey/20.jpg'  id='20' style='cursor: pointer;' onclick=\"location.href='http://globalizationplus.com/survey/receive.php?email=$email&resuslt=20'\"></td>
                    <td style='padding: 35px;'><img src='http://globalizationplus.com/survey/50.jpg'  id='50' style='cursor: pointer;' onclick=\"location.href='http://globalizationplus.com/survey/receive.php?email=$email&resuslt=50'\"></td>
                    <td style='padding: 35px;'><img src='http://globalizationplus.com/survey/80.jpg'  id='80' style='cursor: pointer;' onclick=\"location.href='http://globalizationplus.com/survey/receive.php?email=$email&resuslt=80'\"></td>
                    <td style='padding: 35px;'><img src='http://globalizationplus.com/survey/100.jpg' id='100' style='cursor: pointer;' onclick=\"location.href='http://globalizationplus.com/survey/receive.php?email=$email&resuslt=100'\"></td>
                     -->
                     
                    <td style='padding: 35px;'><a href='http://globalizationplus.com/survey/receive.php?email=$email&result=20' target='_blank'><img src='http://globalizationplus.com/survey/20.jpg'  id='20' style='cursor: pointer;'></a></td>
                    <td style='padding: 35px;'><a href='http://globalizationplus.com/survey/receive.php?email=$email&result=50' target='_blank'><img src='http://globalizationplus.com/survey/50.jpg'  id='50' style='cursor: pointer;'></a></td>
                    <td style='padding: 35px;'><a href='http://globalizationplus.com/survey/receive.php?email=$email&result=80' target='_blank'><img src='http://globalizationplus.com/survey/80.jpg'  id='80' style='cursor: pointer;'></a></td>
                    <td style='padding: 35px;'><a href='http://globalizationplus.com/survey/receive.php?email=$email&result=100' target='_blank'><img src='http://globalizationplus.com/survey/100.jpg' id='100' style='cursor: pointer;></a></td>

                </tr>
                

                <tr>
                
                <td align='left'>&nbsp;</td>
                
                </tr>
               
            </table>
            
            <table align='left' border='0'>
            
             <tr>
                
                <td style='' colspan='4' align='left'>Many thanks for your kind assistance.<br><br><br></td>
                
                </tr>
                
                <tr>
                
                <td style='' colspan='4' align='left'>
                With all best wishes, <br>
                Steve<br><br>
                </td>
                
                </tr>

                <tr>
                
                <td style='' colspan='4' align='left'>
                ****************************************<br>
                Professor Steve Posner, Ed.M., Harvard University<br>
                M.P.W., University of Southern California<br>
                Faculty, The USC Writing Program and<br>
                Dornsife College Interdisciplinary Studies<br>
                University of Southern California<br>
                Email: steve.posner@post.harvard.edu<br>
                Phone: 760.580.8700
                </td>
                </tr>

            </table>

        </div>
    </div>
</body>
</html>";


        return $msg;
    }

    function update_item_err_description($item, $error_info) {
        $query = "update mdl_external_survey_queue "
                . "set email_err='$error_info' where id=$item->id";
        $this->db->query($query);
    }

    function send_single_item($from, $recipient, $subject, $message) {
        $client = new PostmarkClient("5a470ceb-d8d6-49cb-911c-55cbaeec199f");
        $result = $client->sendEmail($from, $recipient, $subject, $message);
        return $result;
    }

    function get_campaign_preface($campid) {
        $query = "select * from mdl_campaign where id=$campid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $preface = $row['preface'];
        }
        return $preface;
    }

    function get_question_answers($qid) {
        $list = "";
        $i = 1;
        $query = "select * from mdl_campaign_a where qid=$qid";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $list.="<table border='0' style='width:100%'>";
            $list.="<tr>";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $list.="<td style='padding:15px;'><a href=''>" . $row['rtext'] . "</a></td>";
                $i++;
            } // end while
            $list.="</tr>";
            $list.="</table>";
        } // end if $num > 0
        return $list;
    }

    function get_message_signature() {
        $list = "";
        $list.="<table>";
        $list.="<tr>
                <td style='' colspan='4' align='left'>Many thanks for your kind assistance.<br><br><br></td>
                </tr>";
        $list.="<tr>
                <td style='' colspan='4' align='left'>
                ****************************************<br>
                Professor Steve Posner, Ed.M., Harvard University<br>
                M.P.W., University of Southern California<br>
                Faculty, The USC Writing Program and<br>
                Dornsife College Interdisciplinary Studies<br>
                University of Southern California<br>
                Email: steve.posner@post.harvard.edu<br>
                Phone: 760.580.8700
                </td>
                </tr>";
        $list.="</table>";
        return $list;
    }

    function get_campaign_questions_block($campid) {
        $list = "";
        $query = "select * from mdl_campaign_q where campid=$campid";
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
                $a = $this->get_question_answers($q->id);
                $text = $q->qtext;
                $list.="<tr>";
                $list.="<td style='padding:15px;'>$text</td>";
                $list.="</tr>";

                $list.="<tr>";
                $list.="<td style=''>$a</td>";
                $list.="</tr>";
            } // end for
            $list.="</table>";
        } // end if $num > 0
        return $list;
    }

    function compose_message($campid) {
        $list = "";
        $preface = $this->get_campaign_preface($campid);
        $questions = $this->get_campaign_questions_block($campid);
        $signature = $this->get_message_signature();

        $list.="<table>";

        $list.="<tr>";
        $list.="<td style=''>$preface</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style=''>$questions</td>";
        $list.="</tr>";

        $list.="<tr>";
        $list.="<td style='padding:15px;'>$signature</td>";
        $list.="</tr>";

        $list.="</table>";
        return $list;
    }

    function preview_campaign($id) {
        $list = "";
        $camp = $this->compose_message($id);

        $list.="<div class='row'>";
        $list.="<div class='col-4'><button class='btn btn-default' id='back_camp'>Back</button><br><br></div>";
        $list.="</div>";

        $list.="<div class='row'>";
        $list.="<div class='col-4'>$camp</div>";
        $list.="</div>";

        return $list;
    }

    function send_survey_email($item) {
        $list = "";

        /*
         * 
          echo "<pre>";
          print_r($item);
          echo "</pre>";
          die();
         * 
         */

        $email = $item->email;
        $campid = $item->campid;
        $from = $this->from;
        $subject = $this->subject;
        $message = $this->compose_message($campid);
        $status = $this->send_single_item($from, $email, $subject, $message);
        if ($status) {
            $list.="Email was successfully sent";
        } // end if
        else {
            $list.="Email was not sent (failure)";
        } // end else
        return $list;
    }

    function send_survey_results($email, $result) {

        switch ($result) {
            case 20:
                $recipient = "20@posnermail.com";
                break;
            case 50:
                $recipient = "50@posnermail.com";
                break;
            case 80:
                $recipient = "80@posnermail.com";
                break;
            case 100:
                $recipient = "100@posnermail.com";
                break;
        }

        //$recipient = 'sirromas@gmail.com'; // temp workaround;
        $date = time();
        $query = "insert into mdl_external_survey_result "
                . "(email,poll_result,added) values('$email','$result','$date')";
        $this->db->query($query);

        $mail = new PHPMailer;

        $message = "";
        $message.="<html>";
        $message.="<body>";
        $message.="<p align='center'>Survey result</p>";

        $message.="<table align='center'>";

        $message.="<tr>";
        $message.="<td style='margin:15px;'>Email</td><td style='margin:15px;'>$email</td>";
        $message.="</tr>";

        $message.="<tr>";
        $message.="<td style='margin:15px;'>Poll result</td><td style='margin:15px;'>$result%</td>";
        $message.="</tr>";

        $message.="</table>";

        $message.="<p>Best regards,</p>";
        $message.="<p>Globalization Plus Team</p>";
        $message.="</body></html>";

        $mail->isSMTP();
        $mail->Host = $this->mail_smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $this->mail_smtp_user;
        $mail->Password = $this->mail_smtp_pwd;
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->mail_smtp_port;

        $mail->setFrom($this->mail_smtp_user, 'Globalization Plus');
        $mail->addAddress($recipient);
        $mail->addReplyTo($this->mail_smtp_user, 'Globalization Plus');

        $mail->isHTML(true);

        $mail->Subject = 'Globalization Plus - Survey Results';
        $mail->Body = $message;

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } // end if !$mail->send()
        else {
            $list = "<br><br><div style='margin:auto;text-align:center;'>Thank you very much!</div>";
            return $list;
        }
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
        $list.="<option value='0' selected>Please select</option>";
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

        $list.="<div class='row' style='padding-top:15px;padding-left:15px;'>";
        $list.="<span class='col-sm-6' style='padding-left:0px;'>$qbox &nbsp;&nbsp;&nbsp;<button class='btn btn-default' id='add_q'>Add Questions</button></span>";
        $list.="</div><br>";

        $list.="<div id='q_container'>";

        $list.="</div>";

        $list.="<div class='row'>";
        $list.="<span class='col-sm-12' id='camp_err' style='padding-top:15px;color:red;'></span>";
        $list.="</div>";

        $list.="<div class='row' style='padding-top:15px;padding-left:15px;'>";
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
            $list.="<span class='col-sm-2'>Choice #$k</span>";
            $list.="<span class='col-sm-6'><input type='text' class='r_$i' style='width:800px;'></span>";
            $list.="</div>";
        }
        return $list;
    }

    function get_questions_block($num) {
        $list = "";
        $list.="<input type='hidden' id='q_num' value='$num'>";
        for ($i = 1; $i <= $num; $i++) {
            $q = $this->get_question_replies_block($i);
            $list.="<div class='row'>";
            $list.="<span class='col-sm-2'>Question #$i</span>";
            $list.="<span class='col-sm-6'><input type='text' id='q_text_$i' style='width:800px;'></span>";
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
        $list = "";
        $title = $camp->title;
        $preface = $camp->content;
        $clear_peface = str_replace("'", "\'", $preface);
        $date = time();
        $query = "insert into mdl_campaign "
                . "(title,preface,added) "
                . "values('$title','$clear_peface','$date')";
        $this->db->query($query);
        $campLastId = $this->get_table_last_record_id('mdl_campaign');

        $questions = $camp->q;
        foreach ($questions as $q) {
            $text = $q->t;
            $clear_text = str_replace("'", "\'", $text);
            $query = "insert into mdl_campaign_q (campid,qtext) "
                    . "values($campLastId,'$clear_text')";
            $this->db->query($query);
            $lastqID = $this->get_table_last_record_id('mdl_campaign_q');
            $answers = $q->a;
            if (count($answers) > 0) {
                foreach ($answers as $a) {
                    $clear_a = str_replace("'", "\'", $a);
                    $query = "insert into mdl_campaign_a (qid, rtext) "
                            . "values($lastqID,'$clear_a')";
                    $this->db->query($query);
                } // end foreach
            } // end if
        }
        $list.=$this->create_camp_list();
        return $list;
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

    function put_item_into_queue($email) {
        $query = "insert into mdl_external_survey_queue "
                . "(email, added) values ('$email', '" . time() . "')";
        $this->db->query($query);
    }

    function upload_emails_list($files) {
        $list = "";
        $file = $files[0];
        if ($file['error'] == 0 && $file['size'] > 0) {
            $filename = time() . rand(10, 175);
            $full_file_path = $this->upload_path . '/' . $filename . '.csv';
            if (move_uploaded_file($file['tmp_name'], $full_file_path)) {
                $csv_data = array_map('str_getcsv', file($full_file_path));
                $csv_array = (count($csv_data[0]) > 1) ? $csv_data[0] : $csv_data[1];

                /*
                  echo "<pre>";
                  print_r($csv_array);
                  echo "</pre>";
                  die();
                 */

                if (count($csv_array) > 0) {
                    foreach ($csv_array as $email) {
                        if ($email != '') {
                            $this->put_item_into_queue($email);
                        }
                    }
                    $list.="Recipients list is put into queue and will be sent soon.";
                } // end if
                else {
                    $list.="No data found";
                }
            } // end if
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
        $query = "select * from mdl_external_survey_queue "
                . "where sent=0 order by added desc limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            $item->id = $row['id'];
            $item->email = $row['email'];
            $status = $this->send_survey_email($item);
            if ($status) {
                $query = "update mdl_external_survey_queue set sent=1 where id=$item->id";
            } // end if $status
            else {
                $query = "update mdl_external_survey_queue set sent=-1 where id=$item->id";
            } //end else
            $this->db->query($query);
        }
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
        $query = "select count(id) as total from mdl_external_survey_result";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $sent = $row['total'];
        }
        $filename = 'users.csv';
        $this->create_csv_file($filename);
        $list.="<span style='font-weight:bold;'>$sent</span><span style='padding-left:8px;'><a href='http://globalizationplus.com/survey/files/users.csv' target='_blank'>Download</a></span>";
        return $list;
    }

    function create_csv_file($filename) {
        // Write CSV data
        $path = $this->upload_path . '/' . $filename;
        $output = fopen($path, 'w');
        fputcsv($output, array('User Email', 'Poll reuslt'));
        $query = "select * from mdl_external_survey_result ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, array($row['email'], $row['poll_result']));
        }
        fclose($output);
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
                                <label for='email'>Email</label>
                                <input type='email' style='width: 235px;' class='form-control' id='email' name='email' placeholder='Enter Email Address'>
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
                $list.="<div clas='row'>";
                $list.="<span style='margin-left:15px;'>Choice #$i&nbsp;&nbsp;<input type='text' class='q_a_$qid' data-id='$id' value='$a' style='width:935px;'></span>";
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

        /*
          echo "<pre>";
          print_r($camp);
          echo "</pre>";
         */

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

}
