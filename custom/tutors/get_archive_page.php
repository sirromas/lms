<!-- Data tables JS -->
<script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
<script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>

<!-- Data tables CSS --> 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">


<?php
require_once './classes/Tutor.php';
$t = new Tutor();
$list = $t->get_archive_page();
echo $list;
?>

<script type="text/javascript">

    $(document).ready(function () {
       $('#archive_table').DataTable();
    });

</script>    