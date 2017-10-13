<?php
require_once './classes/Tutor.php';
$t = new Tutor();
$userid = $_REQUEST['userid'];
$list = $t->get_grades_page($userid);
echo $list;
?>

<script type="text/javascript">

    $(document).ready(function () {
        $('#grades_table').DataTable();
    });

</script>
