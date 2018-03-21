<?php

require_once './sheader.php';

?>

<input type="hidden" id="userid" value="<?php echo $USER->id; ?>">

<!-- Container for all pages loaded via AJAX -->
<div id="ajax_container" style="width: 935px;margin-top: 15px;"></div>

<!-- Article iFrame -->
<div class="row" id="page" style="margin: auto;text-align: center;">
    <iframe id='pageIframe'
            style="margin-top:15px;width:935px;margin-left-30px;text-align: left;"
            frameborder="0"
            src="<?php echo $articleURL; ?>"></iframe>
</div>

<div style="" class="row" id="meeting_container"><?php echo $meetURL; ?></div>


<!-- Dictionary iFrame -->
<div class="row" id="dic"
     style="margin: auto;text-align: center;display: none;">
    <iframe id="dicIframe"
            style="margin-top:15px;width:935px;margin-left-30px;text-align: left;"
            frameborder="0"
            src="<?php echo $dicURL; ?>"></iframe>
</div>

<div id="quiz_container"
     style="width: 935px;margin-top: 15px;display: none;"></div>
<div id="poll_container"
     style="width: 935px;margin-top: 15px;display: none;"></div>

<div id="forum_container" style="width: 935px;margin-top: 15px;"></div>

<div id="copyright_part1" style="width: 935px;text-align: center;">
    <hr>
</div>
<div id="copyright_part2"
     style="width: 935px;text-align: center;margin-bottom: 25px;">© copyright
    2018 by NewsFacts & Analysis. All Rights Reserved.
</div>


<!-- Bootstrap libraries -->
<link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script type="text/javascript"
        src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


<script type="text/javascript">

    $(document).ready(function () {


        var userid = $('#userid').val();
        console.log('User ID: ' + userid);
        var pollURL = '/lms/custom/common/get_news_poll.php';
        var forumURL = '/lms/custom/common/get_news_forum.php';
        var check_picture_url = '/lms/custom/common/is_student_has_picture.php';

        $.post(check_picture_url, {userid: userid}).done(function (data) {
            if (data == '') {
                //var $ = uploadcare.jQuery;
                //$('body').append("<input type='hidden' role='uploadcare-uploader' name='my_file' />");
                var upload_dialog_url = '/lms/custom/common/get_upload_dialog.php';
                $.post(upload_dialog_url, {userid: userid}).done(function (data) {
                    $("body").append(data);
                    $("#myModal").modal('show');
                });
            }
        });


        $('#dicIframe').load(function () {
            $(this).height($(this).contents().height());
            //$(this).width($(this).contents().width());
        });

        var iframe = $('#dicIframe').contents();
        iframe.find(".dnavigation").click(function () {
            console.log('Link inside iframe clicked ...');
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

        function get_teacher_class_grades(item) {
            // We use this URL to display grades for both teacher and student
            var url = '/lms/custom/common/get_teacher_class_grades.php'
            $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                $('#class_grades_container').html(data);
                $('#grades_table').DataTable();
            });
        }


        $('body').on('click', function (event) {


            if (event.target.id == 'upload_my_image') {
                var userid = $('#userid').val();
                var file_url = $('#my_file').val();
                if (file_url == '') {
                    $('#upload_err').html('Please provide file to be uploaded');
                } // end if
                else {
                    $('#upload_err').html('');
                    var item = {userid: userid, file_url: file_url};
                    var url = '/lms/custom/common/upload_user_picture.php';
                    $.post(url, {item: JSON.stringify(item)}).done(function () {
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $('#myModal').data('modal', null);
                        document.location.reload();
                    });
                }
            }

            if (event.target.id == 'submit_poll') {
                var items = [];
                var userid = $('#userid').val();
                var elid = '#total_items_1';
                var total = $(elid).val();
                console.log('Total items: ' + total);
                $('.poll_answers').each(function (index) {
                    if ($(this).is(':checked')) {
                        items.push($(this).val());
                    }
                }); // end of each
                console.log('Poll answers array: ' + JSON.stringify(items));
                if (items.length != total) {
                    alert('Please reply to all questions!');
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
                var elid = '#total_items_2';
                var total = $(elid).val();
                console.log('Total items: ' + total);
                var userid = $('#userid').val();
                $('.quiz_answers').each(function (index) {
                    if ($(this).is(':checked')) {
                        items.push($(this).val());
                    }
                }); // end of each
                console.log('Quiz answers array: ' + JSON.stringify(items));
                if (items.length != total) {
                    alert('Please reply to all questions!');
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
                    var item = {
                        forumid: forumid,
                        userid: userid,
                        text: text,
                        replyto: replyto
                    };
                    var url = '/lms/custom/common/add_forum_post.php';
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log(data);
                        get_news_forum(forumid);
                    });
                }
            }

            if (event.target.id.indexOf("article_id_") >= 0) {
                var id = event.target.id.replace("article_id_", "");
                console.log('Article ID: '+id);
                var elid='#article_id_'+id;
                var url=$(elid).data('url');
                console.log('Article URL: '+url);
                $('#pageIframe').attr("src",url);
                $('#page').show();
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
                    var item = {
                        forumid: forumid,
                        userid: userid,
                        text: text,
                        replyto: replyto
                    };
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


        $('body').on('change', function (event) {

            if (event.target.id == 'teacher_groups') {
                var userid = $('#userid').val();
                var groupid = $('#teacher_groups').val();
                var item = {userid: userid, groupid: groupid};
                if (groupid > 0) {
                    $('#export_grades_container').show();
                    var url = '/lms/custom/common/is_teacher.php';
                    $.post(url, {userid: userid}).done(function (data) {
                        if (data == 0) {
                            $('#ast_container').show();
                        }
                    });
                } // end if
                else {
                    $('#export_grades_container').hide();
                    $('#ast_container').hide();
                } // end else
                get_teacher_class_grades(item);
            }

        }); // end of body.change event

        $(".nav3").click(function () {
            var tag = $(this).data('item');
            var userid = $('#userid').val();
            console.log('Item clicked: ' + tag);
            switch (tag) {
                case 'grades':
                    var url = '/lms/custom/common/get_grades_page.php';
                    $.post(url, {userid: userid}).done(function (data) {
                        $('#ajax_container').html(data);
                        var tableid = '#student_grades_' + userid;
                        $(tableid).DataTable();
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
                    document.location.reload();
                    break;

                case 'quiz':

                    $('#ajax_container').hide();
                    $('#page').hide();
                    $('#dic').hide();
                    $('#poll_container').show();
                    $('#quiz_container').show();
                    $('#forum_container').hide();
                    $('#meeting_container').hide();
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

                case 'dic':
                    $("#dicIframe").attr("src", '<?php echo $dicURL; ?>');
                    $('#dic').show();

                    $('#page').hide();
                    $('#meet').hide();
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

                case 'logout':
                    if (confirm('Logout from the system?')) {
                        var url = '/lms/logout_dashboard.php';
                        $.post(url, {item: 1}).done(function () {
                            window.location = 'https://www.newsfactsandanalysis.com';
                        });
                    }
                    break;
            } // end of switch

        });

    }); // end of document ready


</script>

</body>
</html>



