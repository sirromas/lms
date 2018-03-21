<?php

require_once './Grades.php';
$gr = new Grades();
//$userid = $_REQUEST['userid'];
$userid = $_SESSION['userid'];
if ($userid > 0) {
    $roleid = $gr->get_user_role_by_id($userid);
} // end if
else {
    $roleid = 0;
}
echo $roleid;