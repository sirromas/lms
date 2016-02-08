<?php

require_once './Course.php';
$cs = new Course();
$email=$_POST['email'];
$code=$_POST['code'];
$page=$_POST['page'];
$groups = $_POST['groups'];
if (count($groups)>0) {
    $list = $cs->deleteTutorGroups($email, $code, $page, $groups);
} // end if $groupid!=''
else {
    $list = "<span align='center'>No courses selected</span>";
}
echo $list;

