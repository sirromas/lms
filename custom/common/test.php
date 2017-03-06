<?php

require_once './Utils.php';
$u = new Utils();

$message = "<html><body><br><p style='text-align:center;'>This is aaaaa</p></body></html>";
$recipient = 'alabama@akka.com';
$subject = 'Testing email delivery';
$status = $u->send_email($subject, $message, $recipient);
if ($status) {
    echo "<br><p style='text-align:center;'>Email has been sent to $recipient</p>";
} // end if
else {
    echo "<br><p style='text-align:center;'>Error happened: ";
    echo "<pre>";
    print_r($status);
    echo "</pre></p>";
} // end else
