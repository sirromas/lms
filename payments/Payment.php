<?php
require_once './Classes/Payment.php';
$p = new StudentPayment();
$userid = $_REQUEST['userid'];
$groupslist = $_REQUEST['groups'];
$form = $p->get_student_payment_form($userid, $groupslist);
?>

<!DOCTYPE html>

<html>
    <head>
        <title>GLOBALIZATION PLUS</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!--Typehead library -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js"></script>

        <!-- Custom JS script -->
        <script src="http://globalizationplus.com/assets/js/custom.js"></script>

        <!-- Data tables JS -->
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>

        <!-- Data tables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">


    </head>
    <body>

        <div class="header clearfix">
            <h3 class="text-muted text-center">GLOBALIZATION PLUS</h3>
        </div>

        <br>
        <div class="panel panel-default" style="width:640px;margin: auto;">
            <div class="panel-heading"><div class='text-center'>Subscription renew</div></div>
            <div class="panel-body">
                <?php echo $form; ?>
            </div>
        </div>


    </body>
</html>
