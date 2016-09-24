<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Survey
 *
 * @author moyo
 */
//require_once $_SERVER['DOCUMENT_ROOT'] . '/survey/class.database.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/survey/classes/mailer/vendor/PHPMailerAutoload.php';
//echo __DIR__;
require_once '/homepages/17/d212585247/htdocs/globalizationplus/survey/class.database.php';
//require_once '../class.database.php';
require_once 'mailer/vendor/PHPMailerAutoload.php';

class Survey {

    public $mail_smtp_host = 'smtp.1and1.com';
    public $mail_smtp_port = 25;
    public $mail_smtp_user = 'survey@globalizationplus.com';
    public $mail_smtp_pwd = 'aK6SKymc';
    public $db;
    public $upload_path;

    function __construct() {
        $this->db = new pdo_db();
        $this->mail_smtp_host = $this->get_config_value('smtp_host');
        $this->mail_smtp_port = $this->get_config_value('smtp_port');
        $this->mail_smtp_user = $this->get_config_value('smtp_user');
        $this->mail_smtp_pwd = $this->get_config_value('smtp_password');
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

    function send_survey_email($email) {
        $recipient = $email;
        $message = $this->create_survey_email($email);

        $mail = new PHPMailer;

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

        $mail->Subject = 'Globalization Plus';
        $mail->Body = $message;

        if (!$mail->send()) {
            $list = 'Mailer Error: ' . $mail->ErrorInfo . "\n";
        } // end if !$mail->send()
        else {
            $list = "Email has been sent to $email \n";
        }
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

    function get_settings_page() {
        $list = "";
        $configs = array();
        $query = "select * from mdl_external_survey_config";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $configs[] = $row;
        }

        $list.="<table border='0' align='center'>";
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
                $csv_array = $csv_data[0];
                if (count($csv_array) > 0) {
                    foreach ($csv_array as $email) {
                        $this->put_item_into_queue($email);
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
            $id = $row['id'];
            $email = $row['email'];
            $list = $this->send_survey_email($email);
            echo $list;
            $query = "update mdl_external_survey_queue set sent=1 where id=$id";
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

}
