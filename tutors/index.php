<?php
require_once './classes/Tutor.php';
$t = new Tutor();
$userid = $_REQUEST['userid'];
$form = $t->get_confirmation_form($userid);
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
        
        <!-- Data tables JS -->
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>
        
        <!-- Custom JS script -->
        <script type="text/javascript" src="http://globalizationplus.com/assets/js/custom.js"></script>
     
    </head>
    <body>
        <br><br><br>
        <div class="panel panel-default" style="width:640px;margin: auto;">
            <div class="panel-heading"><div class='text-center'>Membership confirmation</div></div>
            <div class="panel-body">
                <?php echo $form ?>
            </div>
        </div>


    </body>
</html>

