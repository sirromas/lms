<?php

require_once './classes/Utils.php';
$u    = new Utils2();
$post = $_POST;
$file = $_FILES[0];
$u->upload_article_file( $file, $post );

?>

<!-- jQuery library-->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>

<!-- Typehead library-->
<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

<!-- Data tables JS -->
<script type="text/javascript"
        src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
<script type="text/javascript"
        src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js'></script>

<!-- Data tables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

<script type="text/javascript">

    $(document).ready(function () {

        var url = '/lms/utils/get_archive_tab.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#utils_archive').html(data);
        });

    });


</script>

