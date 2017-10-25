<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/access/classes/Access.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/navigation/classes/Navigation.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/students/classes/Student.php';

$ac = new Access();
$nav = new Navigation();


if (!isloggedin()) {
    $url = "https://www.newsfactsandanalysis.com/";
    header("Location: $url");
} // end if


else {

    global $USER;
    $userid = $USER->id;
    $pageid = $nav->get_page_id();
    $pageURL = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/page/view.php?id=$pageid";
    $fourmid = $nav->get_forum_id();
    $forumURL = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/forum/view.php?id=$fourmid";
    $quizid = $nav->get_quiz_id();
    $quizURL = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/quiz/view.php?id=$quizid";
    $dicid = $nav->get_glossary_id();
    $dicURL = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/glossary/view.php?id=$dicid";
    $rgroup = 13; // This is special group for readers only
    $groups = $nav->get_user_groups(); // array

    ?>

    <html>
    <head>
        <meta https-equiv="content-type" content="text/html;charset=utf-8"/>
        <title>NewsFacts & Analysis</title>
        <!-- jQuery library -->
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

        <!-- Bootstrap libraries -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script type="text/javascript"
                src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- Data tables JS -->
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
        <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js'></script>

        <!-- Data tables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

        <!-- Typehead JS -->
        <script type='text/javascript'
                src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

        <!-- Custom JS file -->
        <script type="text/javascript" src="https://www.newsfactsandanalysis.com/assets/js/custom.js"></script>

        <style type="text/css" media="all">
            <!--
            body {
                margin-top: 0px;
                padding-top: 0px;
            }

            #container1 {
                background-color: #000;
                width: 1371px;
                height: 2px;
            }

            a:link {
                color: #000000;
                text-decoration: none;
            }

            a:visited {
                color: #000000;
            }

            a:active {
                color: #000000;
            }

            body {
                color: #000000;
            }

            a:hover {
                color: #5b88f8;
            }

            a.title:hover {
                color: #000000;
                font-weight: bold;
                font-size: 28px;
                font-weight: bold;
            }

            a.dictionary:hover {
                color: #000000;
                font-size: 17px;
                font-weight: normal;
            }

            a.section:hover {
                color: #000000;
                font-size: 16px;
                font-weight: normal;
            }

            .ds5 /*agl rulekind: base;*/
            {
                font-size: 22px;
            }

            .ds19 /*agl rulekind: base;*/
            {
                color: #000;
                font-size: 18px;
            }

            .ds22 /*agl rulekind: base;*/
            {
                color: #000;
            }

            .ds23 /*agl rulekind: base;*/
            {
                color: #fff;
            }

            .ds83 /*agl rulekind: base;*/
            {
                font-family: serif;
                font-size: 18px;
                vertical-align: text-top;
            }

            .ds85 /*agl rulekind: base;*/
            {
                color: #fff;
                font-family: serif;
            }

            .ds203 /*agl rulekind: base;*/
            {
                font-size: 18px;
                font-family: serif;
            }

            #container34 {
                width: 400px;
                height: 10px;
            }

            #container20 {
                width: 400px;
                height: -2px;
            }

            #container15 {
                width: 400px;
                height: 1px;
            }

            #container4 {
                width: 400px;
                height: 8px;
            }

            #container8 {
                width: 685px;
                height: 46px;
            }

            #container10 {
                width: 816px;
                height: 75px;
            }

            #container13 {
                width: 845px;
                height: 11px;
            }

            #container2 {
                background-color: #fff;
                width: 900px;
                height: 232px;
            }

            #container1 {
                background-color: #000;
                width: 900px;
                height: 238px;
                border: solid 1px #000265;
            }

            .dsR2175 /*agl rulekind: base;*/
            {
                width: 876px;
                height: 27px;
            }

            #container29 {
                width: 1024px;
                height: 1px;
            }

            #container30 {
                background-color: #fff;
                width: 400px;
                height: 1px;
            }

            #container32 {
                background-color: #fff;
                width: 900px;
                height: 44px;
            }

            #container33 {
                width: 900px;
                height: 47px;
            }

            #container6 {
                background-color: #000;
                width: 900px;
                height: 47px;
            }

            #container28 {
                width: 1024px;
                height: 3px;
            }

            #container35 {
                width: 400px;
                height: 10px;
            }
        </style>

    </head>

    <body>
    <div align="center">
        <input type="hidden" id="userid" value="<?php echo $USER->id; ?>">
        <span id="header_img"><img src="../assets/images/students_header.png" alt="" height="180" width="907"
                                   border="0"></span>
        <div id="header" style="width:95%;margin:auto;">
            <div style='width:90%;margin:auto;'>
                <div align="center">
                    <div align="center">
                        <div align="center">
                            <div align="center">
                                <div id="container35"></div>
                                <div id="container6">
                                    <div id="container29"></div>
                                    <div id="container33">
                                        <div id="container32">
                                            <div id="container30"></div>


                                            <?php if (!in_array($rgroup, $groups)) {

                                                ?>
                                                <table class="dsR2175" border="0" cellspacing="2" cellpadding="0"
                                                       width="100%">
                                                    <tr>
                                                        <td class="dsR2171">
                                                            <div align="center" style="margin-top:5px;">
                                                                <a href="#" onclick="return false" class="nav3"
                                                                   data-url="<?php echo $pageURL; ?>"
                                                                   data-item="article"><span
                                                                            class="ds203">Article</span></a><span
                                                                        class="ds203">     </span><span
                                                                        class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span
                                                                        class="ds203"></span>

                                                                <a href="#" onclick="return false" class="nav3"
                                                                   data-url="<?php echo $quizURL; ?>"
                                                                   data-item="quiz"><span class="ds203">&nbsp;&nbsp;&nbsp;News Quiz</span></a><span
                                                                        class="ds203">     </span><span
                                                                        class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span
                                                                        class="ds203"></span>

                                                                <a class="nav3" href="#" onclick="return false;"
                                                                   data-url="<?php echo $forumURL; ?>"
                                                                   data-item="forum"><span
                                                                            class="ds203">&nbsp;&nbsp;&nbsp;Dicussion Board</span></a><span
                                                                        class="ds203">     </span><span
                                                                        class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span
                                                                        class="ds203"> </span>

                                                                <a class="nav3" href="#" onclick="return false;"
                                                                   data-url="<?php echo $dicURL; ?>"
                                                                   data-item="dic"><span
                                                                            class="ds203">&nbsp;&nbsp;Political Dictionary</span></a><span
                                                                        class="ds203">     </span><span
                                                                        class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span
                                                                        class="ds203"> </span>

                                                                <a href="#" onclick="return false;" class="nav3"
                                                                   data-url="" data-item="archive">
                                                                    <span class="ds203">&nbsp;Archives&nbsp;&nbsp;&nbsp;</span></a><span
                                                                        class="ds203"></span>
                                                                <span class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span class="ds203">     

                                                            <a href="#" onclick="return false" class="nav3"
                                                               data-url="" data-item="grades">Grades&nbsp;</a>&nbsp;&nbsp;&nbsp;</span>
                                                                <span class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span>

                                                                <a href="https://www.newsfactsandanalysis.com"><span
                                                                            class="ds203">&nbsp;&nbsp;&nbsp;Logout</span></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <?php

                                            } // end if it is university student

                                            else {

                                                ?>

                                                <table class="dsR2175" border="0" cellspacing="2" cellpadding="0"
                                                       width="100%">
                                                    <tr>
                                                        <td class="dsR2171">
                                                            <div align="center" style="margin-top:5px;">
                                                                <a href="#" onclick="return false" class="nav3"
                                                                   data-url="<?php echo $pageURL; ?>"
                                                                   data-item="article"><span
                                                                            class="ds203">Article</span></a><span
                                                                        class="ds203">     </span><span
                                                                        class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span
                                                                        class="ds203"></span>


                                                                <a class="nav3" href="#" onclick="return false;"
                                                                   data-url="<?php echo $dicURL; ?>"
                                                                   data-item="dic"><span
                                                                            class="ds203">&nbsp;&nbsp;Political Dictionary</span></a><span
                                                                        class="ds203">     </span><span
                                                                        class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span
                                                                        class="ds203"> </span>

                                                                <a href="#" onclick="return false;" class="nav3"
                                                                   data-url="" data-item="archive">
                                                                    <span class="ds203">&nbsp;Archives&nbsp;&nbsp;&nbsp;</span></a><span
                                                                        class="ds203"></span>
                                                                <span class="ds19">|</span><span class="ds22"><span
                                                                            class="ds5">|</span></span><span
                                                                        class="ds19">|</span><span class="ds203">     

                                                                <a href="https://www.newsfactsandanalysis.com"><span
                                                                            class="ds203">&nbsp;&nbsp;&nbsp;Logout</span></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>


                                                <?php

                                            }

                                            ?>


                                            <span class="ds83"><span class="ds85"></span></span><a href=""
                                                                                                   style="text-decoration:none"><span
                                                        class="ds23"></span><span class=""><span class=""></span></span></a><span
                                                    class=""></span><span class="ds23"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div id="container28"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="ext_container"
         style="width:935px;margin:auto;text-align:left;height:100%;padding-left:0px;border: none;display:none; "></div>
    <div id="body" style="width:935px;margin:auto;text-align:left;height:100%;padding-left:0px;border: none; ">
        <iframe id='page' style="width:935px;" frameborder="0" src="<?php echo $pageURL; ?>"></iframe>
    </div>
    <!--<div style="width:1024px;margin:auto;text-align:center;"><br/><br/>© copyright 2017 by Executive Clarity. All Rights Reserved.</div>-->
    </body>
    </html>


    <?php
} // end else
?>

<script type="text/javascript">

    $(document).ready(function () {

        $('#page').load(function () {
            //$('#page').src(<?php echo $pageURL; ?>);
            $(this).height($(this).contents().height());
            $(this).width($(this).contents().width());
        });


        //$('#body').hide();
        var userid = $('#userid').val();
        console.log('User ID: ' + userid);


    });


</script>





