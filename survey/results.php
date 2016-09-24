<?php
require_once 'classes/Survey.php';
$survey = new Survey();
$resultObj = $survey->get_poll_results();
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
                    'height': 480};

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>

    </head>

    <body>
        <br/>


        <div id="chart_div" style="padding-left: 25%;"></div>


    </body>
</html>>

