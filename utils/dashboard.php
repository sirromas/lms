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

    $trial_total=$u->get_trial_total();
    $trial=$u->get_trial_keys_tab();
    $trial_search = $u->get_search_block('trial');
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
                        <li class="active"><a data-toggle="tab" href="#classes">Classes</a></li>
                        <li><a data-toggle="tab" href="#tutors">Professors</a></li>
                        <li><a data-toggle="tab" href="#paid_keys">Subscription</a></li>
                        <li><a data-toggle="tab" href="#trial_keys">Trial Keys</a></li>
                        <!--<li><a data-toggle="tab" href="#semester">Semesters</a></li>-->
                        <li><a data-toggle="tab" href="#logout">Account</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="classes" class="tab-pane fade in active">
                            <div style='padding-left: 120px;'><h3>Classes&nbsp;<?php echo $class_search; ?></h3></div>
                            <p><?php echo $classes; ?></p>
                        </div>
                        <div id="tutors" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Professors&nbsp;<?php echo $tutor_search; ?></h3></div>
                            <p><?php echo $tutors; ?></p>
                        </div>
                        <div id="paid_keys" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Subscription&nbsp;<?php echo $subs_search; ?></h3></div>
                            <p><?php echo $subs; ?></p>
                        </div>
                        <div id="trial_keys" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Trial Keys&nbsp;<?php echo $trial_search; ?></h3></div>
                            <p><?php echo $trial; ?></p>
                        </div>
                        
                        <!--
                        <div id="semester" class="tab-pane fade">
                            <div style='padding-left: 120px; '><h3>Semesters</h3></div>
                            <p><?php echo $tutors; ?></p>
                        </div>
                        -->
                        <div id="logout" class="tab-pane fade">
                            <br><br><div style='padding-left: 120px; '><button type='button' class="btn btn-default" id='logout2'>Logout</button></div>
                            <p></p>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">

                $(document).ready(function () {
                    
                    <!-- Classes section -->
                    
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

                    $.get('/lms/utils/data/classes.json', function (data) {
                        $("#search_class").typeahead({source: data, items: 24});
                    }, 'json');
                    
                    <!-- Tutors scetion -->

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
                    
                    
                    $.get('/lms/utils/data/tutors.json', function (data) {
                        $("#search_tutor").typeahead({source: data, items: 24});
                    }, 'json');


                    <!-- Subscription section -->
                    
                    $(function () {
                        $('#subs_paginator').pagination({
                            items: <?php echo $subs_total; ?>,
                            itemsOnPage: <?php echo $u->limit; ?>,
                            cssStyle: 'light-theme'
                        });
                    });

                    $("#subs_paginator").click(function () {
                        var page = $('#subs_paginator').pagination('getCurrentPage');
                        var url = "/lms/utils/get_subs_item.php";
                        $.post(url, {id: page}).done(function (data) {
                            $('#subs_container').html(data);
                        });
                    });

                    $.get('/lms/utils/data/subs.json', function (data) {
                        $("#search_subs").typeahead({source: data, items: 24});
                    }, 'json');
                    
                    
                    <!-- Trial keys section -->
                    
                    $(function () {
                    $('#trial_paginator').pagination({
                            items: <?php echo $trial_total; ?>,
                            itemsOnPage: <?php echo $u->limit; ?>,
                            cssStyle: 'light-theme'
                        });
                    });

                    $("#trial_paginator").click(function () {
                        var page = $('#trial_paginator').pagination('getCurrentPage');
                        var url = "/lms/utils/get_trial_item.php";
                        $.post(url, {id: page}).done(function (data) {
                            $('#trial_container').html(data);
                        });
                    });
                    
                    $.get('/lms/utils/data/trial.json', function (data) {
                        $("#search_trial").typeahead({source: data, items: 24});
                    }, 'json');
                    
                    $('#all').click(function () {
                    var c = this.checked;
                    $(':checkbox').prop('checked', c);
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