<?php
require_once './classes/Tutor.php';
$t = new Tutor();
$list = $t->get_archive_page();
echo "<br/><br/>";
echo $list;
?>

<script type="text/javascript">

    /*
    $(document).ready(function () {
       $('#archive_table').DataTable();
    });
    */

</script>    