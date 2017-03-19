<?php

require_once 'classes/Survey.php';
$survey = new Survey();
$list = $survey->upload_emails_list($_FILES, $_POST);
echo $list;

