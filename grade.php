<?php

require_once './pheader.php';

?>

<input type="hidden" id="userid" value="<?php echo $USER->id; ?>">
<input type="hidden" id="group_users" value="<?php echo $groups_string; ?>">

<!-- Article iFrame -->
<br><br>
<div class="row" id="page" style="margin: auto;text-align: center;">

    <iframe id='pageIframe' style="margin-top:15px;width:935px;margin-left-30px;text-align: left;" frameborder="0"
            src="<?php echo $articleURL; ?>"></iframe>
</div>

<div id="meeting_container"><?php echo $meetURL; ?></div>

<!-- Dictionary iFrame -->
<div class="row" id="dic" style="margin: auto;text-align: center;">
    <iframe id="dicIframe" style="margin-top:15px;width:935px;margin-left-30px;text-align: left;" frameborder="0"
            src="<?php echo $dicURL; ?>"></iframe>
</div>

<!-- Container for all pages loaded via AJAX -->
<div id="ajax_container" style="width: 935px;margin-top: 15px;text-align: left;"></div>

<div id="poll_container" style="width: 935px;margin-top: 15px;display: none;"></div>
<div id="quiz_container" style="width: 935px;margin-top: 15px;display: none;"></div>
<div id="forum_container" style="width: 935px;margin-top: 15px;margin-bottom: 15px;text-align: center;margin-left: 9%"></div><br><br><br>

<div id="copyright_part1" style="width: 935px;text-align: center;"><hr></div>
<div id="copyright_part2" style="width: 935px;text-align: center;margin-bottom: 25px;">© copyright 2018 by NewsFacts & Analysis. All Rights Reserved.</div>

<script type="text/javascript">

    $(document).ready(function () {

        var userid = $('#userid').val();
        console.log('User ID: ' + userid);
        var pollURL = '/lms/custom/common/get_news_poll.php';
        var forumURL = '/lms/custom/common/get_news_forum.php';

        $('#page').hide();
        $('#dic').hide();
        $('#poll_container').hide();
        $('#quiz_container').hide();
        $('#forum_container').hide();
        $('#meeting_container').hide();

        $('#dicIframe').load(function () {
            $(this).height($(this).contents().height());
            //$(this).width($(this).contents().width());
        });

        $('#pageIframe').load(function () {
            $(this).height($(this).contents().height());
            $(this).width($(this).contents().width());
        });

        // Get news poll
        $.post(pollURL, {type: 1}).done(function (data) {
            $('#poll_container').html(data);
            $('#poll_container').height('#poll_container').contents().height();
        });

        // Get news quiz
        $.post(pollURL, {type: 2}).done(function (data) {
            $('#quiz_container').html(data);
            $('#quiz_container').height('#quiz_container').contents().height();
        });

        // Get news forum
        $.post(forumURL, {userid: userid}).done(function (data) {
            $('#forum_container').html(data);
            $('#forum_container').height('#forum_container').contents().height();
        });


        function get_news_forum() {
            $.post(forumURL, {userid: userid}).done(function (data) {
                $('#forum_container').html(data);
                $('#forum_container').height('#forum_container').contents().height();
            });
        }

        // Make grades page first during app open
        var gradesURL = '/lms/custom/common/get_grades_page.php';
        $.post(gradesURL, {userid: userid}).done(function (data) {
            $('#ajax_container').html(data);
            var users = $('#group_users').val();
            console.log('Grpoup users: ' + users);
            var group_users = users.split(",");
            for (i = 0; i < group_users.length; i++) {
                var tableid = '#student_grades_' + group_users[i];
                $(tableid).DataTable();
            }
            $('#grades_table').DataTable();
            $('#ajax_container').show();
        });


        $('body').on('click', function (event) {

            if (event.target.id == 'submit_poll') {
                var items = [];
                var userid = $('#userid').val();
                $('.poll_answers').each(function (index) {
                    if ($(this).is(':checked')) {
                        items.push($(this).val());
                    }
                }); // end of each
                console.log('Poll answers array: ' + JSON.stringify(items));
                if (items.length == 0) {
                    alert('You did not provide any reply!');
                } // end if
                else {
                    if (confirm('Submit research?')) {
                        var url = '/lms/custom/common/submit_quiz_results.php';
                        var item = {userid: userid, items: items};
                        $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                            $('#poll_container').html(data);
                        });
                    }
                }
            }

            if (event.target.id == 'submit_quiz') {
                var items = [];
                var userid = $('#userid').val();
                $('.quiz_answers').each(function (index) {
                    if ($(this).is(':checked')) {
                        items.push($(this).val());
                    }
                }); // end of each
                console.log('Quiz answers array: ' + JSON.stringify(items));
                if (items.length == 0) {
                    alert('You did not provide any reply!');
                } // end if
                else {
                    if (confirm('Submit Quiz?')) {
                        var url = '/lms/custom/common/submit_quiz_results.php';
                        var item = {userid: userid, items: items};
                        $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                            $('#quiz_container').html(data);
                        });
                    }
                }
            }

            if (event.target.id == 'root_reply') {

                if ($('#root_reply_container').is(":visible")) {
                    $('#root_reply_container').hide();
                } // end if
                else {
                    $('#root_reply_container').show();
                }
            }

            if (event.target.id == 'submit_root_reply') {
                var replyto = 0;
                var userid = $('#userid').val();
                var forumid = $('#forumid').val();
                var text = $('#root_reply_text').val();
                if (text != '') {
                    var item = {forumid: forumid, userid: userid, text: text, replyto: replyto};
                    var url = '/lms/custom/common/add_forum_post.php';
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log(data);
                        get_news_forum(forumid);
                    });
                }
            }

            if (event.target.id.indexOf("student_post_reply_") >= 0) {
                var id = event.target.id.replace("student_post_reply_", "");
                var elid = '#student_reply_container_' + id;
                if ($(elid).is(":visible")) {
                    $(elid).hide();
                } // end if
                else {
                    $(elid).show();
                }
            }

            if (event.target.id.indexOf("submit_student_reply_") >= 0) {
                var id = event.target.id.replace("submit_student_reply_", "");
                var replyto = id;
                var userid = $('#userid').val();
                var forumid = $('#forumid').val();
                var elid = '#student_reply_text_' + id;
                var text = $(elid).val();
                console.log()
                if (text != '') {
                    var item = {forumid: forumid, userid: userid, text: text, replyto: replyto};
                    var url = '/lms/custom/common/add_forum_post.php';
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log(data);
                        get_news_forum(forumid);
                    });
                }
            }

            if (event.target.id.indexOf("export_student_grades_") >= 0) {
                var id = event.target.id.replace("export_student_grades_", "");
                var elid = '#export_student_grades_' + id;
                var aid = $(elid).data('aid');
                var userid = $(elid).data('userid');
                var item = {aid: aid, userid: userid};
                var url = '/lms/custom/common/get_csv_student_grades.php';
            }

        }); // end of  body.click event

        $(".nav2").click(function () {
            var tag = $(this).data('item');
            var userid = $('#userid').val();
            console.log('Item clicked: ' + tag);
            switch (tag) {
                case 'grades':
                    var url = '/lms/custom/common/get_grades_page.php';
                    $.post(url, {userid: userid}).done(function (data) {
                        $('#ajax_container').html(data);
                        var users = $('#group_users').val();
                        var group_users = users.split(",");
                        for (i = 0; i < group_users.length; i++) {
                            var tableid = '#student_grades_' + group_users[i];
                            $(tableid).DataTable();
                        }
                        $('#grades_table').DataTable();
                        $('#ajax_container').show();

                        $('#page').hide();
                        $('#dic').hide();
                        $('#poll_container').hide();
                        $('#quiz_container').hide();
                        $('#forum_container').hide();
                        $('#meeting_container').hide();

                    });

                    break;
                case 'article':

                    $("#pageIframe").attr("src", '<?php echo $articleURL; ?>');
                    $('#page').show();

                    $('#poll_container').hide();
                    $('#quiz_container').hide();
                    $('#forum_container').show();

                    $('#dic').hide();
                    $('#ajax_container').hide();
                    $('#meeting_container').show();
                    break;

                case 'archive':
                    var url = '/lms/custom/common/get_archive_page.php';
                    $.post(url, {userid: userid}).done(function (data) {
                        $('#ajax_container').html(data);
                        $('#archive_table').DataTable();
                        $('#ajax_container').show();

                        $('#page').hide();
                        $('#meet').hide();
                        $('#dic').hide();
                        $('#poll_container').hide();
                        $('#quiz_container').hide();
                        $('#forum_container').hide();
                        $('#meeting_container').hide();


                    });
                    break;


                case 'quiz':
                    $('#ajax_container').hide();
                    $('#page').hide();
                    $('#meet').hide();
                    $('#dic').hide();
                    $('#poll_container').show();
                    $('#quiz_container').show();
                    $('#forum_container').hide();
                    $('#meeting_container').hide();

                    break;

                case 'dic':
                    $("#dicIframe").attr("src", '<?php echo $dicURL; ?>');
                    $('#dic').show();

                    $('#page').hide();
                    $('#ajax_container').hide();
                    $('#poll_container').hide();
                    $('#quiz_container').hide();
                    $('#forum_container').hide();
                    $('#meeting_container').hide();

                    break;

                case 'export':
                    var userid = $('#userid').val();
                    var url = '/lms/custom/common/get_export_page.php';
                    $.post(url, {userid: userid}).done(function (data) {
                        $('#ajax_container').html(data);
                        $('#export_table').DataTable();
                        $('#ajax_container').show();

                        $('#page').hide();
                        $('#meet').hide();
                        $('#dic').hide();
                        $('#poll_container').hide();
                        $('#quiz_container').hide();
                        $('#forum_container').hide();
                        $('#ajax_container').hide();
                        $('#meeting_container').hide();

                    });
                    break;

            }

        });

    }); // end of document ready


</script>

</body>
</html>





