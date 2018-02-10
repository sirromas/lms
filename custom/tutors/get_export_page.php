<?php
require_once './classes/Tutor.php';
$t = new Tutor();
$userid = $_REQUEST['userid'];
$list = $t->get_export_page($userid);
echo "<br><br>";
echo $list;
?>

