<?php

require_once './classes/Student.php';
$st = new Student();
$userid = $_REQUEST['userid'];
$list = $st->get_my_grades($userid);
echo "<br/><br/>";
echo $list;

?>

<script type="text/javascript">

    $(document).ready(function () {
        $('#grades_table').DataTable();
    });

</script>
