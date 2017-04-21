<?php

require_once './classes/Utils.php';
$u = new Utils2();
$template = $_POST['template'];
$u->update_email_template(json_decode($template));
