<?php
session_start();
require_once './classes/Utils.php';
$u = new Utils2();
if ($_REQUEST) {
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    if ($username == 'manager' && $password == 'strange12') {
        $_SESSION['logged'] = "1";
    }
}

if ($_SESSION['logged'] == 1) {

    $class_total = $u->get_classes_num();
    $classes = $u->get_classes_list();
    $class_search = $u->get_search_block('class');

    $tutors_total = $u->get_total_tutors_number();
    $tutors = $u->get_tutors_list();
    $tutor_search = $u->get_search_block('tutor');

    $subs_total = $u->get_total_subscription();
    $subs = $u->get_subscription_list();
    $subs_search = $u->get_search_block('subs');

    $trial_total = $u->get_trial_total();
    $trial = $u->get_trial_keys_tab();
    $trial_search = $u->get_search_block('trial');

    $account_tab = $u->get_account_tab();
    ?>

    <!DOCTYPE html>

    <html>
        <head>
            <title>NewsFacts & Analysis</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <!-- ********************** JS libraries ********************** -->

            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

            <!-- Latest compiled and minified JavaScript -->
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

            <!-- Editor CDN -->
            <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>

            <!-- Typehead JS -->
            <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

            <!-- Pagination JS -->
            <script type="text/javascript" src="/assets/js/pagination/jquery.simplePagination.js"></script>

            <!-- Data tables JS -->
            <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
            <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>

            <!-- Custom JS script -->
            <script type="text/javascript" src="http://www.newsfactsandanalysis.com/assets/js/custom.js"></script>

            <!-- ********************** CSS libraries ********************** -->

            <!-- Latest compiled and minified Bootstrap CSS -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >

            <!-- Optional theme -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >

            <!-- DatePicker JS -->
            <script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

            <!-- DatePicker CSS -->
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

            <!-- Data tables CSS -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

            <!-- Pagination CSS -->
            <link rel="stylesheet" href="/assets/js/pagination/simplePagination.css">

        </head>
        <body>
            <br><br><br>
            <div class="panel panel-default" style="width:80%;margin: auto;">
                <div class="panel-heading"><div class='text-center'>Dashboard</div></div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#classes">Classes</a></li>
                        <li><a data-toggle="tab" href="#tutors">Professors</a></li>
                        <li><a data-toggle="tab" href="#paid_keys">Subscription</a></li>
                        <li><a data-toggle="tab" href="#trial_keys">Trial Keys</a></li>
                        <li><a data-toggle="tab" href="#logout_account">Account</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="classes" class="tab-pane fade in active">
                            <?php echo $classes; ?>
                        </div>
                        <div id="tutors" class="tab-pane fade">
                            <?php echo $tutors; ?>
                        </div>
                        <div id="paid_keys" class="tab-pane fade">
                            <?php echo $subs; ?>
                        </div>
                        <div id="trial_keys" class="tab-pane fade">
                            <?php echo $trial; ?>
                        </div>
                        <div id="logout_account" class="tab-pane fade">
                            <br><br><?php echo $account_tab; ?></div>
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
} // end if
else {
    header('Location: http://www.newsfactsandanalysis.com/lms/utils');
    exit;
}
?>