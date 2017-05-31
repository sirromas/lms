<?php
require_once 'classes/Survey.php';

if ($_POST) {
    $survey = new Survey();
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (!$survey->check_user($username, $password)) {
        header('Location: http://globalizationplus.com/survey/');
        exit();
    } // end if !$survey->check_user($username, $password)
} // end if $_POST
else {
    header('Location: http://globalizationplus.com/survey/');
    exit();
} // end else
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

        <!-- Progress bar CSS -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >

        <!-- Data tables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

        <!--Typehead library -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!--Load the AJAX API-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- Editor CDN -->
        <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>

        <!-- Highcharts JS -->
        <script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>       
        <script src="https://code.highcharts.com/highcharts-3d.js"></script>
        <script src="https://code.highcharts.com/highcharts-3d.js"></script>

        <!-- Custom JS code -->
        <script type="text/javascript" src="../assets/js/custom.js"></script>

        <!-- Data tables JS -->
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>

        <!-- Color picker libraries -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>
        <link href="//cdnjs.cloudflare.com/ajax/libs/octicons/3.5.0/octicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css">

        <!-- jQuery UI -->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    </head>

    <body>
        <br/>

        <ul class="nav nav-tabs" style="padding-left: 35px;">
            <li class="active"><a data-toggle="tab" href="#home">Send emails</a></li>
            <li><a data-toggle="tab" href="#menu1">Settings</a></li>
            <li><a data-toggle="tab" href="#camp">Survey</a></li>
            <li><a data-toggle="tab" href="#progress">Progress</a></li>
            <li><a data-toggle="tab" href="#menu2">Results</a></li>
            <li><a data-toggle="tab" href="#menu3">Account</a></li>
        </ul>

        <div class="tab-content" style="padding-left: 5px;">
            <div id="home" class="tab-pane fade in active">
                <?php
                $send_email = $survey->get_send_email_page();
                echo $send_email;
                ?>
            </div>

            <div id="menu1" class="tab-pane fade" style="width:640px;padding-left: 45px;padding-top: 25px; ">
                <?php
                $settings = $survey->get_settings_page();
                echo $settings;
                ?>
            </div>
            <div id="menu2" class="tab-pane fade" style="padding-left: 45px;padding-top: 25px; ">
                <?php
                $result = $survey->get_results_page();
                ?>

                <div style="padding-left: 15px;"><?php echo $result; ?></div>
                <div class="row" id="camp_result" style='padding-left: 32px;'></div>

            </div>
            <div id="camp" class="tab-pane fade" style="width:1024px;padding-left: 45px;padding-top: 25px; ">
                <?php
                $camp = $survey->get_campaign_page();
                echo $camp;
                ?>
            </div>

            <div id="progress" class="tab-pane fade" style="width:1024px;padding-left: 45px;padding-top: 25px; ">
                <?php
                $progress = $survey->get_campaigns_progress();
                echo $progress;
                ?>         
            </div>

            <div id="menu3" class="tab-pane fade" style="width:640px;padding-left: 45px;padding-top: 25px; ">
                <div style="padding-left: 45px;"><button type="submit" id='logout' class="btn btn-default">Logout</button></div>
            </div>

        </div>

        <script type="text/javascript">

            $(document).ready(function () {

            });




        </script>


    </body>
</html>
