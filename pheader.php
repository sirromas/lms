<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/access/classes/Access.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/navigation/classes/Navigation.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/students/classes/Student.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Archive.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Grades.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Forum.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Quiz.php';

$ac  = new Access();
$nav = new Navigation();

if ( ! isloggedin() ) {
	$url = "https://www.newsfactsandanalysis.com/";
	header( "Location: $url" );
} // end if

else {

global $USER;
$userid     = $USER->id;
$articleURL = $nav->get_article_url();

$dicURL = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/dictionary/index.php";
$rgroup = 13; // This is special group for readers only
$groups = $nav->get_user_groups(); // array

$ar      = new Archive();
$archive = $ar->get_archive_page();

$gr         = new Grades();
$gradesPage = $gr->get_grades_page( $userid );
$groupname  = $gr->get_group_name( $gr->get_postuser_group( $userid ) ) ;
$meetURL    = "https://demo.bigbluebutton.org/b/meetings/$groupname";

?>


<!DOCTYPE html>

<html>
<head>
    <title>NewsFacts &amp; Analysis</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jQuery library -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

    <!-- Bootstrap libraries -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Data tables JS -->
    <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
    <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js'></script>

    <!-- Data tables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

    <!-- Font awesome icons -->
    <script src="https://use.fontawesome.com/42565977b1.js"></script>

    <!-- Typehead JS -->
    <script type='text/javascript'
            src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

    <!-- Custom JS file -->
    <!--<script type="text/javascript" src="https://www.newsfactsandanalysis.com/assets/js/custom.js"></script>-->

    <!-- PDF Library -->
    <script type="text/javascript" src="https://www.newsfactsandanalysis.com/assets/js/pdf/pdfobject.js"></script>


    <style type="text/css" media="all">

        body {
            margin-top: 0px;
            padding-top: 0px;
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
            color: #000000;
            font-size: 16px;
            font-weight: bold;
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

        .ds76 /*agl rulekind: base;*/
        {
            color: #000;
            font-size: 48px;
            font-family: serif;
        }

        .ds80 /*agl rulekind: base;*/
        {
            color: #000;
            font-size: 18px;
            font-family: serif;
        }

        .ds83 /*agl rulekind: base;*/
        {
            font-family: serif;
            font-size: 18px;
            vertical-align: text-top;
        }

        .ds84 /*agl rulekind: base;*/
        {
            color: #000;
            font-size: 22px;
            font-family: serif;
        }

        .ds85 /*agl rulekind: base;*/
        {
            color: #fff;
            font-family: serif;
        }

        .ds153 /*agl rulekind: base;*/
        {
            font-size: 17px;
            font-family: cursive;
        }

        .ds154 /*agl rulekind: base;*/
        {
            font-size: 15px;
        }

        .ds155 /*agl rulekind: base;*/
        {
            font-size: 15px;
            font-family: cursive;
        }

        .ds179 /*agl rulekind: base;*/
        {
            color: #830;
            font-size: 48px;
            font-family: serif;
        }

        .ds180 /*agl rulekind: base;*/
        {
            color: #830;
            font-size: 22px;
            font-family: serif;
        }

        .ds197 /*agl rulekind: base;*/
        {
            color: #830;
            font-size: 60px;
            font-family: serif;
        }

        .ds199 /*agl rulekind: base;*/
        {
            color: #000;
            font-size: 60px;
            font-family: serif;
        }

        .ds200 /*agl rulekind: base;*/
        {
            font-size: 17px;
            font-family: serif;
        }

        .ds202 /*agl rulekind: base;*/
        {
            font-size: 18px;
        }

        .ds203 /*agl rulekind: base;*/
        {
            font-size: 18px;
            font-family: serif;
        }

        .ds204 /*agl rulekind: base;*/
        {
            color: #fff;
            font-size: 18px;
            font-family: serif;
        }

        .ds207 /*agl rulekind: base;*/
        {
            font-size: 30px;
        }

        #container37 {
            background-color: #fff;
            width: 906px;
            height: 320px;
            border-style: solid;
            border-width: 2px 1px 1px;
        }

        #container38 {
            background-color: #cccccc;
            background-image: url(/assets/images/assignmentbackground2.jpg);
            width: 941px;
            height: 341px;
            border-style: solid;
            border-width: 3px 1px 2px 2px;
        }

        #container36 {
            background-color: #fff;
            width: 972px;
            height: 358px;
            border-style: solid;
            border-width: 6px 1px 3px;
        }

        .dsR2216 /*agl rulekind: base;*/
        {
            width: 606px;
            height: 12.6px;
        }

        .dsR2217 /*agl rulekind: base;*/
        {
            width: 876px;
            height: 27px;
        }

        .dsR2218 /*agl rulekind: base;*/
        {
            width: 896px;
            height: 278px;
        }

        #container39 {
            width: 400px;
            height: 10px;
        }

        #container21 {
            width: 400px;
            height: -2px;
        }

        #container16 {
            width: 400px;
            height: 1px;
        }

        #container5 {
            width: 400px;
            height: 8px;
        }

        #container9 {
            width: 685px;
            height: 43px;
        }

        #container11 {
            width: 816px;
            height: 75px;
        }

        #container14 {
            width: 845px;
            height: 11px;
        }

        #container3 {
            background-color: #fff;
            width: 877px;
            height: 232px;
        }

        #container7 {
            background-color: #000;
            width: 882px;
            height: 238px;
            border: solid 1px #000265;
        }

        #container40 {
            width: 400px;
            height: 10px;
        }

        #container31 {
            width: 400px;
            height: 1px;
        }

        #container41 {
            background-color: #fff;
            width: 400px;
            height: 1px;
        }

        #container42 {
            background-color: #fff;
            width: 878px;
            height: 44px;
        }

        #container43 {
            width: 878px;
            height: 47px;
        }

        #container12 {
            background-color: #000;
            width: 880px;
            height: 47px;
        }

        #container44 {
            width: 400px;
            height: 3px;
        }

        #container34 {
            width: 400px;
            height: 10px;
        }

    </style>


</head>
<body>
<div align="center">
    <div align="center">
        <div id="container34"></div>
        <div id="container36">
            <div id="container38">
                <div align="center">
                    <div id="container37">
                        <div class="row dsR2218">
                            <div align="center">
                                <div align="center">
                                    <div id="container39"></div>
                                </div>
                                <div id="container21"></div>
                                <div id="container7">
                                    <div align="center">
                                        <div id="container3">
                                            <div align="center">
                                                <div align="center">
                                                    <div id="container14">
                                                        <div align="center">
                                                            <div id="container16"></div>
                                                            <span class="ds153"></span>
                                                            <div id="container5"></div>
                                                            <div id="container9">
                                                                <span class="ds155">&quot;Whatever may be our wishes, our inclination, or the dictates of our passions, they cannot</span><span
                                                                        class="ds154"><br>
																		</span><span class="ds155">alter the state of facts and evidence.&quot; </span><span
                                                                        class="ds154">—</span><span class="ds155"> John Adams, President of the United States, 1797-1801</span>
                                                            </div>
                                                            <img class="dsR2216" src="../assets/images/line.gif" alt=""
                                                                 border="0">
                                                            <div id="container11">
                                                                <span class="ds197">N</span><span class="ds180"> </span><span
                                                                        class="ds179">e</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">w</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">s</span><span class="ds5"><span
                                                                            class="ds180"> </span></span><span
                                                                        class="ds197">F</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">a</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">c</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">t</span><span
                                                                        class="ds180"> </span><span class="ds179">s &amp;</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds199">A</span><span
                                                                        class="ds84"> </span><span class="ds76">n</span><span
                                                                        class="ds84"> </span><span class="ds76">a</span><span
                                                                        class="ds84"> </span><span class="ds76">l</span><span
                                                                        class="ds84"> </span><span class="ds76">y</span><span
                                                                        class="ds84"> </span><span class="ds76">s</span><span
                                                                        class="ds84"> </span><span class="ds76">i</span><span
                                                                        class="ds84"> </span><span class="ds76">s</span><span
                                                                        class="ds180"> </span></div>
                                                            <p><span class="ds80">nonpartisan news assignments with no political, religious, or ideological affliliation of any kind</span>
                                                            </p>
                                                            <p><span class="ds83"><span class="ds207"><span
                                                                                class="ds207">Professor's Gradebook</span></span></span>
                                                            </p>
                                                            <p><span class="ds83"><span class="ds200"></span></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div align="center">
                                    <div align="center">
                                        <div id="container40"></div>
                                        <div id="container12">
                                            <div id="container31"></div>
                                            <div id="container43">
                                                <div id="container42">
                                                    <div id="container41"></div>
                                                    <table class="dsR2217" border="0" cellspacing="2" cellpadding="0">
                                                        <tr>
                                                            <td class="dsR2171">
                                                                <div align="center">

                                                                    <!-- Article -->
                                                                    <a href="#" class="nav2" data-item="article"
                                                                       onclick="return false;"><span
                                                                                class="ds203">Article</span></a><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds22"><span
                                                                                class="ds5">|</span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span>

                                                                    <!-- Dictionary -->
                                                                    <span class="ds203"><a
                                                                                href="#" class="nav2"
                                                                                onclick="return false;" data-item="dic">Dictionary</a></span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds22"><span
                                                                                class="ds5">|</span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span>

                                                                    <!-- Archives -->
                                                                    <span class="ds203"><a
                                                                                href="#" class="nav2"
                                                                                onclick="return false;"
                                                                                data-item="archive">Archives</a></span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds22"><span
                                                                                class="ds5">|</span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds202"><span
                                                                                class="ds19">  </span></span>

                                                                    <!-- Grades -->
                                                                    <span class="ds203"><a
                                                                                href="#" class="nav2"
                                                                                onclick="return false;"
                                                                                data-item="grades">Grades</a></span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds22"><span
                                                                                class="ds5">|</span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span>

                                                                    <!-- Class Room -->
                                                                    <span class="ds203"><a class="nav2" target="_blank"
                                                                                           href="<?php echo $meetURL; ?>">Class Room</a></span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds22"><span
                                                                                class="ds5">|</span></span><span
                                                                            class="ds19">|</span><span
                                                                            class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                            class="ds83"><span
                                                                                class="ds204"> </span></span>

                                                                    <!-- Logout -->
                                                                    <span class="ds203">
                                                                        <a href="https://www.newsfactsandanalysis.com"><span
                                                                                    class="ds203">Logout</span></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <span class="ds83"><span class="ds85"></span></span><a
                                                            href="https://www.newsfactsandanalysis.com/registerstudent.html"
                                                            style="text-decoration:none"><span class="ds23"></span><span
                                                                class=""><span class=""></span></span></a><span
                                                            class=""></span><span class="ds23"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


								<?php } ?>



