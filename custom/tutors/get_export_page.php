<?php
require_once './classes/Tutor.php';
$t = new Tutor();
$userid = $_REQUEST['userid'];
$list = $t->get_export_page($userid);
echo $list;
?>

<script type="text/javascript">

    $(document).ready(function () {
        $('#export_table').DataTable();
    });

</script>
