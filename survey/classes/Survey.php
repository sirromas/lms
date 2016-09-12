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

    function send_survey_results($title, $email, $result) {

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


        $mail = new PHPMailer;
      
        $message = "";
        $message.="<html>";
        $message.="<body>";
        $message.="<p align='center'>Survey result</p>";

        $message.="<table align='center'>";

        $message.="<tr>";
        $message.="<td style='margin:15px;'>Title</td><td style='margin:15px;'>$title</td>";
        $message.="</tr>";

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
            $list = "Thank you very much!";
            return $list;
        }
    }

}
