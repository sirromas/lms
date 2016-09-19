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
require_once $_SERVER['DOCUMENT_ROOT'] . '/survey/classes/mailer/vendor/PHPMailerAutoload.php';

class Survey {

    public $mail_smtp_host = 'smtp.1and1.com';
    public $mail_smtp_port = 25;
    public $mail_smtp_user = 'survey@globalizationplus.com';
    public $mail_smtp_pwd = 'aK6SKymc';

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

    function process_emails() {

        $emails[] = 'sirromas@gmail.com';

        if (count($emails) > 0) {
            foreach ($emails as $email) {
                $this->send_survey_email($email);
            }
        }
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
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } // end if !$mail->send()
        else {
            $list = "Email has been sent to $email <br>";
            return $list;
        }
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
            $list = "<br><br><p align='center'>Thank you very much!</p>";
            return $list;
        }
    }

}
