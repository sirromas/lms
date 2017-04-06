<?php
require_once './classes/Archive.php';
$a = new Archive();

if ($_REQUEST) {
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $status = $a->verify_user($username);
    $_SESSION['userid'] = $status;
} // end if $_REQUEST

if ($_SESSION['userid'] > 0) {
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

            <!-- Typehead JS -->
            <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

            <!-- Data tables JS -->
            <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
            <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>

            <!-- Data tables CSS -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

            <!-- Custom JS script -->
            <script type="text/javascript" src="http://globalizationplus.com/assets/js/custom.js"></script>

            <script type="text/javascript">

                $(document).ready(function () {
                    $('#news_table').DataTable();
                    $('#forum_table').DataTable();
                    $('#quiz_table').DataTable();
                });

            </script>    



            <!-- DatePicker JS -->
            <script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

            <!-- DatePicker CSS -->
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

        </head>
        <body>
            <br><br><br>
            <div class="panel panel-default" style="width:80%;margin: auto;">
                <div class="panel-heading"><div class='text-center'>Dashboard</div></div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#articles">Articles</a></li>
                        <li><a data-toggle="tab" href="#forums">Forums</a></li>
                        <li><a data-toggle="tab" href="#quizes">Quizzes</a></li>
                        <li><a data-toggle="tab" href="#logout_account">Account</a></li>
                    </ul>
                    <?php
                    $articles = $a->get_articles_archive();
                    $forums=$a->get_forums_archive();
                    $quizzes=$a->get_quiz_archive();
                    ?>
                    <div class="tab-content">
                        <div id="articles" class="tab-pane fade in active">
                            <?php echo $articles; ?>
                        </div>
                        <div id="forums" class="tab-pane fade">
                            <?php echo $forums; ?>
                        </div>
                        <div id="quizes" class="tab-pane fade">
                            <?php echo $quizzes; ?>
                        </div>

                        <div id="logout_account" class="tab-pane fade">
                            <br><br><div style='padding-left: 120px; '><button type='button' class="btn btn-default" id='logout_account_archive'>Logout</button></div>
                            <p></p>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">

                $(document).ready(function () {

                    $('#classes_table').DataTable();
                    $('#tutors_table').DataTable();
                    $('#subs_table').DataTable();
                    $('#trial_table').DataTable();

                }); // end of document ready



            </script>

        </body>
    </html>

    <?php
} // end if $_SESSION['logged']==1
else {
    header('Location: http://globalizationplus.com/lms/archive');
    exit;
}
?>