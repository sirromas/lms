<?php
require_once './classes/Archive.php';
$a = new Archive();
?>

<!DOCTYPE html>

<html>
    <head>
        <title>NewsFacts &amp; Analysis</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        
        <!-- Typehead library -->
        <script type="text/javascript" src="/assets/js/typehead.js"></script>
        
        <!-- Custom JS script -->
        <script type="text/javascript" src="http://www.newsfactsandanalysis.com/assets/js/custom.js"></script>

        <!-- Data tables JS -->
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>

        <!-- Data tables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">


    </head>
    <body>
        <br><br><br>
        <div class="panel panel-default" style="width:640px;margin: auto;">
            <div class="panel-heading"><div class='text-center'>Login</div></div>
            <div class="panel-body">
                <form action="http://www.newsfactsandanalysis.com/lms/archive/dashboard.php" method="post" id="archieve_login">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="username">Email*:</label>
                            <input type="text" class="form-control" required="" name="username" id="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password*:</label>
                            <input type="password" class="form-control" required="" name="password" id="password">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Login</button>
                        </div>
                        <div class="form-group" id='ajax_loader' style="text-align: center;display: none;">
                            <img src="http://www.newsfactsandanalysis.com/assets/images/load.gif" height="50" width="450" alt="loading" />
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>


    </body>
</html>