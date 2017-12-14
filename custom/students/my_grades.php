<?php

require_once './classes/Student.php';
$st = new Student();
$userid = $_REQUEST['userid'];
//echo "User ID: ".$userid."<br>";
$list = $st->get_my_grades($userid);
echo $list;

?>

<script type="text/javascript">

    $(document).ready(function () {
        $('#grades_table').DataTable();
    });

</script>
