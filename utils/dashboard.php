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

    $tutors_total = $u->get_total_tutors_number();
    $tutors = $u->get_tutors_list();
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

            <!-- Pagination JS -->
            <script type="text/javascript" src="/assets/js/pagination/jquery.simplePagination.js"></script>

            <!-- Pagination CSS -->
            <link rel="stylesheet" href="/assets/js/pagination/simplePagination.css">

            <!-- Custom JS script -->
            <script type="text/javascript" src="http://globalizationplus.com/assets/js/custom.js"></script>


        </head>
        <body>
            <br><br><br>
            <div class="panel panel-default" style="width:80%;margin: auto;">
                <div class="panel-heading"><div class='text-center'>Dashboard</div></div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#classes">Classes</a></li>
                        <li><a data-toggle="tab" href="#tutors">Professors</a></li>
                        <li><a data-toggle="tab" href="#paid_keys">Paid Keys</a></li>
                        <li><a data-toggle="tab" href="#trial_keys">Trial Keys</a></li>
                        <li><a data-toggle="tab" href="#logout">Logout</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="classes" class="tab-pane fade in active">
                            <div style='padding-left: 120px; '><h3 >Classes</h3></div>
                            <p><?php echo $classes; ?></p>
                        </div>
                        <div id="tutors" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Professors</h3></div>
                            <p><?php echo $tutors; ?></p>
                        </div>
                        <div id="paid_keys" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Paid Keys</h3></div>
                            <p>Some content in menu 2.</p>
                        </div>
                        <div id="trial_keys" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Trial Keys</h3></div>
                            <p>Some content in menu 3.</p>
                        </div>
                        <div id="logout" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Logout</h3></div>
                            <p>Some content in menu 4.</p>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">

                $(document).ready(function () {

                    $(function () {
                        $('#class_paginator').pagination({
                            items: <?php echo $class_total; ?>,
                            itemsOnPage: <?php echo $u->limit; ?>,
                            cssStyle: 'light-theme'
                        });
                    });

                    $("#class_paginator").click(function () {
                        var page = $('#class_paginator').pagination('getCurrentPage');
                        var url = "/lms/utils/get_class_item.php";
                        $.post(url, {id: page}).done(function (data) {
                            $('#classes_container').html(data);
                        });
                    });

                    $(function () {
                        $('#tutors_paginator').pagination({
                            items: <?php echo $tutors_total; ?>,
                            itemsOnPage: <?php echo $u->limit; ?>,
                            cssStyle: 'light-theme'
                        });
                    });

                    $("#tutors_paginator").click(function () {
                        var page = $('#tutors_paginator').pagination('getCurrentPage');
                        var url = "/lms/utils/get_tutor_item.php";
                        $.post(url, {id: page}).done(function (data) {
                            $('#tutors_container').html(data);
                        });
                    });


                }); // end of ducment ready



            </script>

        </body>
    </html>

    <?php
} // end if
else {
    header('Location: http://globalizationplus.com/lms/utils');
    exit;
}
?>