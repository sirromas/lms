<?php

$url = $_REQUEST['url'];

?>

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

    <!-- Font awesome -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js"
            integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l"
            crossorigin="anonymous"></script>
    <script defer
            src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js"
            integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c"
            crossorigin="anonymous"></script>

    <!-- PDF Library -->
    <script type="text/javascript"
            src="https://www.newsfactsandanalysis.com/assets/js/pdf/pdfobject.js"></script>

    <link rel="stylesheet" href="../../body.css">
    <link rel="stylesheet" href="../../quiz.css">


</head>
<body>

<div style="wmargin: auto;text-align: center;padding-left: 15%;margin-top: 25px;">
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
                                 src="../../../assets/images/line.gif"
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- End of container7 -->

<!-- Article iFrame -->
<div class="row" id="page"
     style="margin: auto;text-align: center;">
    <iframe id='pageIframe'
            style="width:935px;margin-left-30px;text-align: left;"
            frameborder="0"
            src="<?php echo $url; ?>"></iframe>
</div>


<script type="text/javascript">

    $(document).ready(function () {

        $('#pageIframe').load(function () {
            $(this).height($(this).contents().height());
            $(this).width($(this).contents().width());
        });

    }); // end of document ready

</script>

</body>
</html>