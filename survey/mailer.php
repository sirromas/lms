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

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >


        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!--Load the AJAX API-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <script type="text/javascript" src="../assets/js/custom.js"></script>

    </head>

    <body>
        <br/>

        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Send emails</a></li>
            <li><a data-toggle="tab" href="#menu1">Settings</a></li>
            <li><a data-toggle="tab" href="#menu2">Results</a></li>
            <li><a data-toggle="tab" href="#menu3">Account</a></li>
        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="panel panel-default" style="width:640px;padding-left: 45px;padding-top: 25px; ">
                    <div class="panel-heading"><div class='text-center'>Emails sender</div></div>
                    <div class="panel-body">
                        <form action="launch.php" method="post" id="launcher" name="launcher">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Or select CSV file to be uploaded:</label>
                                <input id="file" name="file" type="file" class="file">
                            </div>
                            <div class="form-group">
                                <span id="form_err"></span>
                            </div>    
                            <button type="submit" class="btn btn-default">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div id="menu1" class="tab-pane fade" style="width:640px;padding-left: 45px;padding-top: 25px; ">
                <?php
                $settings = $survey->get_settings_page();
                echo $settings;
                ?>
            </div>
            <div id="menu2" class="tab-pane fade" style="width:640px;padding-left: 45px;padding-top: 25px; ">
                <?php
                $resultObj = $survey->get_poll_results();
                $queue = $survey->get_queue_status();
                ?>
                <script type="text/javascript">

                    // Load the Visualization API and the corechart package.
                    google.charts.load('current', {'packages': ['corechart']});

                    // Set a callback to run when the Google Visualization API is loaded.
                    google.charts.setOnLoadCallback(drawChart);

                    // Callback that creates and populates a data table,
                    // instantiates the pie chart, passes in the data and
                    // draws it.
                    function drawChart() {

                        // Create the data table.
                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Percentage');
                        data.addColumn('number', 'Students');
                        data.addRows([
                            ['20% of Students', <?php echo (string) $resultObj->p20; ?>],
                            ['50% of Students', <?php echo (string) $resultObj->p50; ?>],
                            ['80% of Students', <?php echo (string) $resultObj->p80; ?>],
                            ['100% of Students ', <?php echo (string) $resultObj->p100; ?>]
                        ]);

                        // Set chart options
                        var options = {'title': 'Poll results',
                            'width': 640,
                            'height': 420};

                        // Instantiate and draw our chart, passing in some options.
                        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                        chart.draw(data, options);
                    }
                </script>

                <div id="chart_div" style="padding-left: 45px;"></div>
                <div style="border-style: dashed;"><span id='q'><?php echo $queue; ?></span><span style='cursor: pointer;'><img src='http://globalizationplus.com/assets/images/refresh.png' width='45' height='35' id='r' title='Refresh'></span></div>


            </div>

            <div id="menu3" class="tab-pane fade" style="width:640px;padding-left: 45px;padding-top: 25px; ">
                <div style="padding-left: 45px;"><button type="submit" id='logout' class="btn btn-default">Logout</button></div>
            </div>

        </div>



    </body>
</html>
