<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/access/classes/Access.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/navigation/classes/Navigation.php';

$ac = new Access();
$nav = new Navigation();

if (!isloggedin()) {
    $url = "http://www.newsfactsandanalysis.com/";
    header("Location: $url");
} // end if
else {

    global $USER;
    $courseid = $nav->get_actual_course_id();
    $pageid = $nav->get_page_id();
    $pageURL = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/page/view.php?id=$pageid";
    $fourmid = $nav->get_forum_id();
    $forumURL = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/forum/view.php?id=$fourmid";
    $quizid = $nav->get_quiz_id();
    $quizURL = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/quiz/view.php?id=$quizid";
    $dicid = $nav->get_glossary_id();
    $dicURL = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/glossary/view.php?id=$dicid";
    $user = $nav->get_user_details($USER->id);
    $username = $user->email;
    $archiveURL = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/archive/dashboard.php?username=$username";
    $eXLS = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/grade/export/xls/index.php?id=$courseid";
    $eTXT = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/grade/export/txt/index.php?id=$courseid";
    $groups = $ac->get_user_groups();
    $groupid = $groups[0];
    $gradeURL = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/grade/report/grader/index.php?id=" . $ac->courseid . "&group=$groupid";
    ?>

    <html>
        <head>
            <meta http-equiv="content-type" content="text/html;charset=utf-8" />
            <title>NewsFacts & Analysis</title>
            <!-- jQuery library -->
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

            <!-- Data tables JS -->
            <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ></script>
            <script type="text/javascript" src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js' ></script>

            <!-- Typehead JS -->
            <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

            <!-- Custom JS file -->
            <script type="text/javascript" src="http://www.newsfactsandanalysis.com/assets/js/custom.js"></script>

            <style type="text/css" media="all">
                <!-- 
                body {margin-top: 0px; padding-top: 0px; }
                #container1 { background-color: #000; width: 1371px; height: 2px; }
                a:link { color: #000000; text-decoration: none; }
                a:visited { color: #000000; }
                a:active { color: #000000; }
                body { color: #000000; }
                a:hover { 
                    color: #000000; font-size: 16px; font-weight: bold;
                }
                a.title:hover { 
                    color: #000000; font-weight: bold; font-size: 28px; font-weight: bold;
                }
                a.dictionary:hover { 
                    color: #000000;  font-size: 17px; font-weight: normal;
                }
                a.section:hover { 
                    color: #000000;  font-size: 16px; font-weight: normal;
                }
                .ds5 /*agl rulekind: base;*/ { font-size: 22px; }
                .ds19 /*agl rulekind: base;*/ { color: #000; font-size: 18px; }
                .ds22 /*agl rulekind: base;*/ { color: #000; }
                .ds23 /*agl rulekind: base;*/ { color: #fff; }
                .ds76 /*agl rulekind: base;*/ { color: #000; font-size: 48px; font-family: serif; }
                .ds80 /*agl rulekind: base;*/ { color: #000; font-size: 18px; font-family: serif; }
                .ds83 /*agl rulekind: base;*/ { font-family: serif; font-size: 18px; vertical-align: text-top; }
                .ds84 /*agl rulekind: base;*/ { color: #000; font-size: 22px; font-family: serif; }
                .ds85 /*agl rulekind: base;*/ { color: #fff; font-family: serif; }
                .ds106 /*agl rulekind: base;*/ { text-align: left; text-indent: 0; margin: 0; }
                .ds153 /*agl rulekind: base;*/ { font-size: 17px; font-family: cursive; }
                .ds154 /*agl rulekind: base;*/ { font-size: 15px; }
                .ds155 /*agl rulekind: base;*/ { font-size: 15px; font-family: cursive; }
                .ds179 /*agl rulekind: base;*/ { color: #830; font-size: 48px; font-family: serif; }
                .ds180 /*agl rulekind: base;*/ { color: #830; font-size: 22px; font-family: serif; }
                .ds197 /*agl rulekind: base;*/ { color: #830; font-size: 60px; font-family: serif; }
                .ds199 /*agl rulekind: base;*/ { color: #000; font-size: 60px; font-family: serif; }
                .dsR2160 /*agl rulekind: base;*/ { width: 890px; height: 264px;text-align:center; }
                .ds200 /*agl rulekind: base;*/ { font-size: 17px; font-family: serif; }
                .ds202 /*agl rulekind: base;*/ { font-size: 18px; }
                .ds203 /*agl rulekind: base;*/ { font-size: 18px; font-family: serif; }
                .ds204 /*agl rulekind: base;*/ { color: #fff; font-size: 18px; font-family: serif; }
                #container34 { width: 400px; height: 10px; }
                #container20 { width: 400px; height: -2px; }
                #container15 { width: 400px; height: 1px; }
                #container4 { width: 400px; height: 8px; }
                #container8 { width: 685px; height: 43px; }
                #container10 { width: 816px; height: 75px; }
                #container13 { width: 845px; height: 11px; }
                #container2 { background-color: #fff; width: 877px; height: 232px; }
                #container1 { background-color: #000; width: 882px; height: 238px; border: solid 1px #000265; }
                .dsR2183 /*agl rulekind: base;*/ { width: 904px; height: 298px; }
                .dsR2185 /*agl rulekind: base;*/ { width: 896px; height: 278px; }
                .dsR2175 /*agl rulekind: base;*/ { width: 876px; height: 27px; }
                #container29 { width: 1024px; height: 1px; }
                #container30 { background-color: #fff; width: 400px; height: 1px; }
                #container32 { background-color: #fff; width: 878px; height: 44px; }
                #container33 { width: 878px; height: 47px; }
                #container6 { background-color: #000; width: 880px; height: 47px; }
                #container28 { width: 1024px; height: 3px; }
                #container35 { width: 400px; height: 10px; }
                .ds207 /*agl rulekind: base;*/ { font-size: 30px; }
                .dsR2215 /*agl rulekind: base;*/ { width: 606px; height: 12.6px; }
                -->
            </style>

        </head>

        <body>

            <div id="header" style="width:95%;margin:auto;">
                <div style='width:90%;margin:auto;'>
                    <div align="center">
                        <div class="container dsR2160">
                            <div class="text-center dsR2183">
                                <div class="row dsR2185">
                                    <div align="center">
                                        <div align="center">
                                            <div id="container34"></div>
                                        </div>
                                        <div id="container20"></div>
                                        <div id="container1">
                                            <div align="center">
                                                <div id="container2">
                                                    <div align="center">
                                                        <div align="center">
                                                            <div id="container13">
                                                                <div align="center">
                                                                    <div id="container15"></div>
                                                                    <span class="ds153"></span>
                                                                    <div id="container4"></div>
                                                                    <div id="container8">
                                                                        <span class="ds155">Whatever may be our wishes, our inclination, or the dictates of our passions, they cannot</span><span class="ds154"><br>
                                                                        </span><span class="ds155">alter the state of facts and evidence.&quot; </span><span class="ds154">—</span><span class="ds155"> John Adams, President of the United States, 1797-1801</span></div>
                                                                    <img class="dsR2215" src="../assets/images/line.gif" alt="" border="0">
                                                                    <div id="container10">
                                                                        <span class="ds197">N</span><span class="ds180"> </span><span class="ds179">e</span><span class="ds180"> </span><span class="ds179">w</span><span class="ds180"> </span><span class="ds179">s</span><span class="ds5"><span class="ds180"> </span></span><span class="ds197">F</span><span class="ds180"> </span><span class="ds179">a</span><span class="ds180"> </span><span class="ds179">c</span><span class="ds180"> </span><span class="ds179">t</span><span class="ds180"> </span><span class="ds179">s &amp;</span><span class="ds84">  </span><span class="ds199">A</span><span class="ds84"> </span><span class="ds76">n</span><span class="ds84"> </span><span class="ds76">a</span><span class="ds84"> </span><span class="ds76">l</span><span class="ds84"> </span><span class="ds76">y</span><span class="ds84"> </span><span class="ds76">s</span><span class="ds84"> </span><span class="ds76">i</span><span class="ds84"> </span><span class="ds76">s</span><span class="ds180"> </span></div>
                                                                    <p><span class="ds80">nonpartisan news assignments with no political, religious, or ideological affliliation of any kind</span></p>
                                                                    <p><span class="ds83"><span class="ds207"><span class="ds207">Professor's Gradebook</span></span></span></p>
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
                                                <div id="container35"></div>
                                                <div id="container6">
                                                    <div id="container29"></div>
                                                    <div id="container33">
                                                        <div id="container32">
                                                            <div id="container30"></div>
                                                            <table class="dsR2175" border="0" cellspacing="2" cellpadding="0" width="100%">
                                                                <tr>
                                                                    <td class="dsR2171">
                                                                        <div align="center">
                                                                            <a href="#" onclick="return false" class="nav2" data-url="<?php echo $pageURL; ?>"><span class="ds203">Article</span> </a> <span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds19">|</span><span class="ds22"><span class="ds5">|</span></span><span class="ds19">|</span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span> <a href="#" onclick="return false" class="nav2" data-url="<?php echo $gradeURL; ?>"><span class="ds203">Grades</span></a><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds19">|</span><span class="ds22"><span class="ds5">|</span></span><span class="ds19">|</span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds203"><a href="#" onclick="return false" class="nav2" data-url="<?php echo $forumURL; ?>">Discussion</a></span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds19">|</span><span class="ds22"><span class="ds5">|</span></span><span class="ds19">|</span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds203"><a href="#" onclick="return false;" class="nav2" data-url="<?php echo $quizURL; ?>">Quiz</a></span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds19">|</span><span class="ds22"><span class="ds5">|</span></span><span class="ds19">|</span><span class="ds202"><span class="ds19">  </span></span><span class="ds203"><a href="#" class='nav2' onclick="return false;" data-url="<?php echo $dicURL; ?>">Dictionary</a></span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds19">|</span><span class="ds22"><span class="ds5">|</span></span><span class="ds19">|</span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds203"><a href="#" onclick="return false;" class='nav2' data-url='<?php echo $archiveURL; ?>'>Archives</a></span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds19">|</span><span class="ds22"><span class="ds5">|</span></span><span class="ds19">|</span><span class="ds202"><span class="ds19">  </span></span><span class="ds203"><a href="#" onclick="return false;" class='nav2' data-url='<?php echo $eXLS; ?>'>Export Grades: Excel</a></span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds19">|</span><span class="ds22"><span class="ds5">|</span></span><span class="ds19">|</span><span class="ds202"><span class="ds19"> </span></span><span class="ds83"><span class="ds204"> </span></span><span class="ds203"><a href="#" onclick="return false;" class='nav2' data-url='<?php echo $eTXT; ?>'>Export Grades: Text</a></span></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <span class="ds83"><span class="ds85"></span></span><a href="" style="text-decoration:none"><span class="ds23"></span><span class=""><span class=""></span></span></a><span class=""></span><span class="ds23"></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="container28"></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="ds106" align="center"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="body"  style="width:935px;margin:auto;text-align:left;height:100%;padding-top:45px;padding-left:15px;border: none; ">
                <iframe id='page' style="text-align:left;" src="<?php echo $gradeURL; ?>" frameborder="0"></iframe>
            </div>
            <br/><br/>


            <!--<div style="width:1024px;margin:auto;text-align:center;"><br/><br/>© copyright 2017 by Executive Clarity. All Rights Reserved.</div>-->


        </body>
    </html>


    <?php
} // end else
?>

<script type="text/javascript">

    $(document).ready(function () {
        $('#page').load(function () {
            $(this).height($(this).contents().height());
            $(this).width($(this).contents().width());
        });
    });


</script>





