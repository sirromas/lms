<?php
require_once './classes/Utils.php';
$u = new Utils2();
if ($_REQUEST) {
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $status = $u->authorize($username, $password);
    if ($status == 1) {
        $_SESSION['logged'] = 1;
    } // end if 
    else {
        header('Location: https://www.newsfactsandanalysis.com/lms/utils');
        exit;
    }

    if ($_SESSION['logged'] == 1) {

        $archive = $u->get_archive_page();

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

	    $publish=$u->get_publish_page();
        $quiz=$u->get_news_quiz_page();
        $forum=$u->get_news_forum_page();
        $onlineClasses=$u->get_online_classes_page();
        $prices = $u->get_prices_page();

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
            <script type='text/javascript'
                    src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

            <!-- Pagination JS -->
            <script type="text/javascript" src="/assets/js/pagination/jquery.simplePagination.js"></script>

            <!-- Data tables JS -->
            <script type="text/javascript"
                    src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
            <script type="text/javascript"
                    src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js'></script>

            <!-- Custom JS script -->
            <script type="text/javascript" src="https://www.newsfactsandanalysis.com/assets/js/custom.js"></script>

            <!-- ********************** CSS libraries ********************** -->

            <!-- Latest compiled and minified Bootstrap CSS -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

            <!-- Optional theme -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

            <!-- DatePicker JS -->
            <script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

            <!-- DatePicker CSS -->
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

            <!-- Data tables CSS -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

            <!-- Pagination CSS -->
            <link rel="stylesheet" href="/assets/js/pagination/simplePagination.css">

            <style>

                .fileUpload {
                    position: relative;
                    overflow: hidden;
                    margin: 10px;
                }

                .fileUpload input.upload {
                    position: absolute;
                    top: 0;
                    right: 0;
                    margin: 0;
                    padding: 0;
                    font-size: 20px;
                    cursor: pointer;
                    opacity: 0;
                    filter: alpha(opacity=0);
                }

                .stepwizard-step p {
                    margin-top: 10px;
                }
                .stepwizard-row {
                    display: table-row;
                }
                .stepwizard {
                    display: table;
                    width: 50%;
                    position: relative;
                }
                .stepwizard-step button[disabled] {
                    opacity: 1 !important;
                    filter: alpha(opacity=100) !important;
                }
                .stepwizard-row:before {
                    top: 14px;
                    bottom: 0;
                    position: absolute;
                    content: " ";
                    width: 100%;
                    height: 1px;
                    background-color: #ccc;
                    z-order: 0;
                }
                .stepwizard-step {
                    display: table-cell;
                    text-align: center;
                    position: relative;
                }
                .btn-circle {
                    width: 30px;
                    height: 30px;
                    text-align: center;
                    padding: 6px 0;
                    font-size: 12px;
                    line-height: 1.428571429;
                    border-radius: 15px;
                }


            </style>

        </head>
        <body>
        <br><br><br>
        <div class="panel panel-default" style="width:80%;margin: auto;">
            <div class="panel-heading">
                <div class='text-center'>Dashboard</div>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#classes">Classes</a></li>
                    <li><a data-toggle="tab" href="#tutors">Professors</a></li>
                    <li><a data-toggle="tab" href="#paid_keys">Subscription</a></li>
                    <li><a data-toggle="tab" href="#trial_keys">Trial Keys</a></li>
                    <li><a data-toggle="tab" href="#prices">Prices</a></li>
                    <li><a data-toggle="tab" href="#publish">Articles</a></li>
                    <li><a data-toggle="tab" href="#quiz">Quiz</a></li>
                    <li><a data-toggle="tab" href="#forum">Board</a></li>
                    <li><a data-toggle="tab" href="#oclasses">Online Classes</a></li>
                    <!--<li><a data-toggle="tab" href="#utils_archive">Archive</a></li>-->
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
                    <div id="publish" class="tab-pane fade">
		                <?php echo $publish; ?>
                    </div>
                    <div id="quiz" class="tab-pane fade">
		                <?php echo $quiz; ?>
                    </div>
                    <div id="forum" class="tab-pane fade">
		                <?php echo $forum; ?>
                    </div>
                    <div id="oclasses" class="tab-pane fade">
                        <?php echo $onlineClasses; ?>
                    </div>
                    <div id="prices" class="tab-pane fade">
                        <?php echo $prices; ?>
                    </div>
                    <div id="utils_archive" class="tab-pane fade">
                        <?php echo $archive; ?>
                    </div>
                    <div id="logout_account" class="tab-pane fade">
                        <br><br><?php echo $account_tab; ?></div>
                    <p></p>
                </div>
            </div>
        </div>
        </div>

        <!-- Date Time Picker -->
        <link rel="stylesheet" type="text/css" href="/assets/js/jquery.datetimepicker.css"/ >
        <script src="/assets/js/jquery.datetimepicker.full.min.js"></script>

        <script type="text/javascript">

            $(document).ready(function () {

                $('#classes_table').DataTable();
                $('#tutors_table').DataTable();
                $('#subs_table').DataTable();
                $('#price_table').DataTable();
                $('#trial_table').DataTable();
                $('#archive_table').DataTable();
                $('#poll_table').DataTable();
                $('#forum_table').DataTable();
                $('#online_classes_table').DataTable();


                // ***** Articles code *****
                $('#a_date1').datepicker();
                $('#a_date2').datepicker();

                // ***** Video chat code *****
                $.get('/lms/utils/data/groups.json', function (data) {
                    $("#oclass_classes").typeahead({source: data, items: 256000});
                });
                $('#oclass_date').datetimepicker();


                // ***** Semestr duration **********
                $('#first_semestr_start_text').datepicker();
                $('#first_semestr_end_text').datepicker();
                $('#second_semestr_start_text').datepicker();
                $('#second_semestr_end_text').datepicker();



            }); // end of document ready


        </script>

        </body>
        </html>

        <?php
    } // end if
    else {
        header('Location: https://www.newsfactsandanalysis.com/lms/utils');
        exit;
    }
} // end if request
else {
    header('Location: https://www.newsfactsandanalysis.com/lms/utils');
    exit;
} // end else
?>