<?php


require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/access/classes/Access.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/navigation/classes/Navigation.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/students/classes/Student.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Archive.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Grades.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Forum.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Quiz.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/51/core/51Degrees.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/51/core/51Degrees_usage.php';

$ac  = new Access();
$nav = new Navigation();
$mobile = $_51d['IsMobile'];
$userid = $_GET['userid'];

$_SESSION['userid'] = $userid;
$_SESSION['mobile'] = $mobile;

if ($userid == '' || $userid == 0) {
    $url = "https://www.newsfactsandanalysis.com/";
    header("Location: $url");
} // end if

else {

echo "<input type='hidden' id='userid' value='$userid'>";
echo "<input type='hidden' id='mobile' value='$mobile'>";
$articleURL = $nav->get_article_url();

$dicURL = "http://www.newsfactsandanalysis.com/dictionary/politicaldictionary.html";
$rgroup = 13; // This is special group for readers only
$groups = $nav->get_user_groups(); // array

$ar      = new Archive();
$archive = $ar->get_archive_page();

$gr      = new Grades();
$meetURL = $gr->get_meeting_url($userid);

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
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript"
            src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Data tables JS -->
    <script type="text/javascript"
            src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
    <script type="text/javascript"
            src='https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js'></script>

    <!-- Data tables CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">

    <!-- Font awesome icons -->
    <script src="https://use.fontawesome.com/42565977b1.js"></script>

    <!-- Typehead JS -->
    <script type='text/javascript'
            src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.js'></script>

    <!-- PDF Library -->
    <script type="text/javascript" src="https://www.newsfactsandanalysis.com/assets/js/pdf/pdfobject.js"></script>

    <!-- Uploadcare Library -->
    <script>UPLOADCARE_PUBLIC_KEY = "8893d57d59b8970385fb";</script>
    <script src="https://ucarecdn.com/libs/widget/3.2.3/uploadcare.full.min.js" charset="utf-8"></script>

    <link rel="stylesheet" href="./body.css">
    <link rel="stylesheet" href="./quiz.css">


</head>
<body>

<?php if ($mobile) { ?>

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#" style="font-weight: bold;">NF&A</a>
            </div>

            <!-- Navigation links -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#" class="nav3" data-item="article">Article <span class="sr-only">(current)</span></a>
                    </li>
                    <li><a href="#" class="nav3" data-item="quiz">News Quiz</a></li>
                    <li><a href="#" class="nav3" data-item="dic">Political Dictionary</a></li>
                    <li><a href="#" class="nav3" data-item="archive">Archives</a></li>
                    <li><a href="#" class="nav3" data-item="grades">Grades</a></li>
                    <li>
                        <a href="https://www.newsfactsandanalysis.com/about.html" class="nav2" target="_blank">About
                            Us</a>
                    </li>
                    <li><a href="mailto:info@newsfactsandanalysis.com" class="nav2">Contact Us</a></li>
                    <li><a href="#" class="nav2" data-item="logout">Logout</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-->
    </nav>
    </nav>


<?php }

else {?>
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
																		</span><span
                                                                        class="ds155">alter the state of facts and evidence.&quot; </span><span
                                                                        class="ds154">—</span><span
                                                                        class="ds155"> John Adams, President of the United States, 1797-1801</span>
                                                            </div>
                                                            <img class="dsR2216"
                                                                 src="../assets/images/line.gif"
                                                                 alt=""
                                                                 border="0">
                                                            <div id="container11">
                                                                <span class="ds197">N</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">e</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">w</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">s</span><span
                                                                        class="ds5"><span
                                                                            class="ds180"> </span></span><span
                                                                        class="ds197">F</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">a</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">c</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">t</span><span
                                                                        class="ds180"> </span><span
                                                                        class="ds179">s &amp;</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds199">A</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds76">n</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds76">a</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds76">l</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds76">y</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds76">s</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds76">i</span><span
                                                                        class="ds84"> </span><span
                                                                        class="ds76">s</span><span
                                                                        class="ds180"> </span>
                                                            </div>
                                                            <p>
                                                                <span class="ds80">nonpartisan news assignments with no political, religious, or ideological affliliation of any kind</span>
                                                            </p>
                                                            <p><span class="ds83"><span
                                                                            class="ds207"><span
                                                                                class="ds207">Text, Video, Comments</span></span></span>
                                                            </p>
                                                            <p>
                                                                <span class="ds83"><span
                                                                            class="ds200"></span></span>
                                                            </p>
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
                                                    <table class="dsR2217"
                                                           border="0"
                                                           cellspacing="2"
                                                           cellpadding="0">
                                                        <tr>
                                                            <td class="dsR2171">
                                                                <div align="center">

                                                                    <!-- Article -->
                                                                    <span class="ds203">
                                                                    <a href="#"
                                                                       class="nav3"
                                                                       data-item="article"
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
                                                                                class="ds83">

                                                                    <!-- Quiz -->
                                                                    <span class="ds203"><a
                                                                                href="#"
                                                                                data-item="quiz"
                                                                                class="nav3"
                                                                                onclick="return false;">News Quiz</a></span><span
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
                                                                                    class="ds83">


                                                                    <!-- Dic -->
                                                                    <span class="ds203"><a
                                                                                href="#"
                                                                                data-item="dic"
                                                                                class="nav3"
                                                                                onclick="return false;">Political Dictionary</a></span><span
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
                                                                                        class="ds83">

                                                                        <!-- Archives -->
                                                                        <span class="ds204"> </span></span><span
                                                                                        class="ds203"><a
                                                                                            href="#"
                                                                                            class="nav3"
                                                                                            data-item="archive"
                                                                                            onclick="return false;">Archives</a></span><span
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
                                                                                href="#"
                                                                                class="nav3"
                                                                                data-item="grades"
                                                                                onclick="return false;">Grades</a></span><span
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

                                                                                <!-- About Us -->
                                                                    <span class="ds203"><a
                                                                                href="https://www.newsfactsandanalysis.com/about.html"
                                                                                class="nav2"
                                                                                target="_blank">About Us</a></span><span
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

                                                                                <!-- Contact Us -->
                                                                    <span class="ds203"><a
                                                                                href="mailto:info@newsfactsandanalysis.com"
                                                                                class="nav2">Contact Us</a></span>
                                                                    <span class="ds202"><span
                                                                                class="ds19"> </span></span><span
                                                                                        class="ds83"><span
                                                                                            class="ds204"> </span></span><span
                                                                                        class="ds19">|</span><span
                                                                                        class="ds22"><span
                                                                                            class="ds5">|</span></span><span
                                                                                        class="ds19">|</span><span
                                                                                        class="ds202"><span
                                                                                            class="ds19"> </span></span>

                                                                                <!-- Logout -->
                                                                        <span class="ds204"> </span></span>
                                                                <a href="#" class="nav3" data-item="logout" onclick="return false;"><span
                                                                            class="ds203">Logout</span></a>


                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <span class="ds83"><span
                                                                class="ds85"></span></span><a
                                                            href=""
                                                            style="text-decoration:none"><span
                                                                class="ds23"></span><span
                                                                class=""><span
                                                                    class=""></span></span></a><span
                                                            class=""></span><span
                                                            class="ds23"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php } ?>





