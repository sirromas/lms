<?php

require_once './pheader.php';

?>

<input type="hidden" id="userid" value="<?php echo $USER->id; ?>">
<input type="hidden" id="group_users" value="<?php echo $groups_string; ?>">

<!-- Container for all pages loaded via AJAX -->
<br><br>
<div id="ajax_container"
     style="width: 935px;margin-top: 15px;text-align: left;"></div>

<!-- Article iFrame -->
<div class="row" id="page" style="margin: auto;text-align: center;">

    <iframe id='pageIframe'
            style="margin-top:15px;width:935px;margin-left-30px;text-align: left;"
            frameborder="0"
            src="<?php echo $articleURL; ?>"></iframe>
</div>

</div>

<div id="meeting_container"><?php echo $meetURL; ?></div>

<!-- Dictionary iFrame -->
<div class="row" id="dic" style="margin: auto;text-align: center;">
    <iframe id="dicIframe"
            style="margin-top:15px;width:935px;margin-left-30px;text-align: left;"
            frameborder="0"
            src="<?php echo $dicURL; ?>"></iframe>
</div>


<div id="quiz_container"
     style="width: 935px;margin-top: 15px;display: none;"></div>
<div id="poll_container"
     style="width: 935px;margin-top: 15px;display: none;"></div>

<div id="forum_container"
     style="width: 935px;margin-top: 15px;margin-bottom: 15px;text-align: center;margin-left: 9%"></div>
<br><br><br>

<div id="copyright_part1" style="width: 935px;text-align: center;">
    <hr>
</div>
<div id="copyright_part2"
     style="width: 935px;text-align: center;margin-bottom: 25px;">© copyright
    2018 by NewsFacts & Analysis. All Rights Reserved.
</div>

<script type="text/javascript">

    $(document).ready(function () {

        /* Regular code */
        var userid = $('#userid').val();
        console.log('User ID: ' + userid);
        var pollURL = '/lms/custom/common/get_news_poll.php';
        var forumURL = '/lms/custom/common/get_news_forum.php';

        //$('#page').hide();
        $('#dic').hide();
        $('#poll_container').hide();
        $('#quiz_container').hide();
        //$('#forum_container').hide();
        //$('#meeting_container').hide();

        $('#dicIframe').load(function () {
            $(this).height($(this).contents().height());
            // Detect click inside iFrame? Is it possible?
            var iframe = $('#dicIframe').contents();
            iframe.find('.ds249').click(function () {
                console.log('Item clicked ....');
            });
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
            // item.userid, item.groupid

            // We use this URL to display grades for both teacher and student
            var url = '/lms/custom/common/get_teacher_class_grades.php'
            $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                $('#class_grades_container').html(data);
                $('#grades_table').DataTable();
            });
        }

        // Make grades page first during app open
        /*
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
            // $('#grades_table').DataTable();
            $('#ajax_container').show();
        });
        */


        $('body').on('click', function (event) {

            var eclass = $(event.target).attr('class');
            console.log('Element class: ' + eclass);

            if (event.target.id.indexOf("article_id_") >= 0) {
                var id = event.target.id.replace("article_id_", "");
                console.log('Article ID: ' + id);
                var elid = '#article_id_' + id;
                var url = $(elid).data('url');
                console.log('Article URL: ' + url);
                $('#pageIframe').attr("src", url);
                $('#page').show();
            }

            // ************* Poll grades ****************
            if (event.target.id == 'back_to_class_grades') {
                var userid = $('#back_to_class_grades').data('teacherid');
                var groupid = $('#back_to_class_grades').data('groupid');
                var item = {userid: userid, groupid: groupid};
                get_teacher_class_grades(item);
            }

            if (event.target.id.indexOf("edit_poll_grades_") >= 0) {
                console.log('Edit poll grades clicked ...');
                var teacherid = $('#userid').val();
                var groupid = $('#teacher_groups').val();
                var token = event.target.id.replace('edit_poll_grades_', '');
                var elid = '#edit_poll_grades_' + token;
                var aid = $(elid).data('aid');
                var type = $(elid).data('type');
                var userid = $(elid).data('userid');
                var item = {
                    teacherid: teacherid,
                    groupid: groupid,
                    aid: aid,
                    type: type,
                    userid: userid
                };
                console.log('Item: ' + JSON.stringify(item));
                var url = '/lms/custom/common/get_edit_grades_dialog.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    $('#class_grades_container').html(data);
                });
            }

            if (event.target.id.indexOf("edit_quiz_grades_") >= 0) {
                console.log('Edit quiz grades clicked ...');
                var teacherid = $('#userid').val();
                var groupid = $('#teacher_groups').val();
                var token = event.target.id.replace('edit_quiz_grades_', '');
                var elid = '#edit_quiz_grades_' + token;
                var aid = $(elid).data('aid');
                var type = $(elid).data('type');
                var userid = $(elid).data('userid');
                var item = {
                    teacherid: teacherid,
                    groupid: groupid,
                    aid: aid,
                    type: type,
                    userid: userid
                };
                console.log('Item: ' + JSON.stringify(item));
                var url = '/lms/custom/common/get_edit_grades_dialog.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    $('#class_grades_container').html(data);
                });
            }

            if (event.target.id.indexOf("posts_details_") >= 0) {
                console.log('Post details clicked ...');

                var teacherid = $('#userid').val();
                var groupid = $('#teacher_groups').val();

                var token = event.target.id.replace('posts_details_', '');
                var elid = '#posts_details_' + token;
                var aid = $(elid).data('aid');
                var userid = $(elid).data('userid');

                var item = {
                    teacherid: teacherid,
                    groupid: groupid,
                    aid: aid,
                    userid: userid
                };
                console.log('Item: ' + JSON.stringify(item));
                var url = '/lms/custom/common/get_student_post_details.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    $('#class_grades_container').html(data);
                });
            }


            if (event.target.id == 'update_student_grades') {
                var replies = [];
                var studentid = $('#studentid').val();
                var old_answers = $('#old_answers').val();
                console.log('Student id: ' + studentid);
                $('input[type="radio"]:checked').each(function () {
                    replies.push($(this).data('id'));
                });
                var item = {
                    studentid: studentid,
                    replies: replies,
                    old_answers: old_answers
                };
                console.log('Item: ' + JSON.stringify(item));
                if (confirm('Update student grades?')) {
                    var url = '/lms/custom/common/update_student_grades.php';
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log('Server response: ' + data);
                        var userid = $('#back_to_class_grades').data('teacherid');
                        var groupid = $('#back_to_class_grades').data('groupid');
                        var item = {userid: userid, groupid: groupid};
                        get_teacher_class_grades(item);
                    });
                }
            }

            // ***************** Classes section *****************


            if (event.target.id == 'cancel_dialog') {
                //document.location.reload();
            }

            if (event.target.id == 'add_new_class') {
                var userid = $('#userid').val();
                var id = Math.round((new Date()).getTime() / 1000);
                var url = '/lms/custom/common/get_add_new_class_dialog.php';
                var item = {userid: userid, id: id};
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    var modalID = '#' + id;
                    $("body").append(data);
                    $(modalID).modal('show');
                });
            }

            if (event.target.id.indexOf("add_new_class_done_") >= 0) {
                var id = event.target.id.replace('add_new_class_done_', '');
                var userid = $('#userid').val();
                var groupelid = '#gname_' + id;
                var gname = $(groupelid).val();
                var errelid = '#gname_err_' + id;
                if (gname == '') {
                    $(errelid).html('Please provide class name');
                } // end if
                else {
                    $(errelid).html('');
                    var check_url = '/lms/custom/common/is_group_exists.php';
                    $.post(check_url, {gname: gname}).done(function (status) {
                        if (status > 0) {
                            $(errelid).html('Provided class name already exists');
                            return false;
                        } // end if
                        else {
                            $(errelid).html('');
                            var item = {userid: userid, gname: gname};
                            var url = '/lms/custom/common/add_new_class_done.php';
                            $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                                $("[data-dismiss=modal]").trigger({type: "click"});
                                var update_url = '/lms/custom/common/update_teacher_classes_list.php';
                                $.post(update_url, {userid: userid}).done(function (data) {
                                    $('#teacher_classes_container').html(data);
                                }) // end of pust
                            }); // end of post
                        } // end else
                    }); // end of post
                } // end else
            }

            // ***************** Assistance section *****************

            if (event.target.id == 'add_assistance') {
                var userid = $('#userid').val();
                var id = Math.round((new Date()).getTime() / 1000);
                var url = '/lms/custom/common/get_add_assistance_dialog.php';
                var item = {userid: userid, id: id};
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    var modalID = '#' + id;
                    $("body").append(data);
                    $(modalID).modal('show');
                });
            }


            if (event.target.id == 'add_assistance_done') {
                var teacherid = $('#userid').val();
                var fname = $('#fname').val();
                var lname = $('#lname').val();
                var email = $('#email').val();
                var pwd = $('#pwd').val();
                var groupid = $('#teacher_groups').val();
                if (fname == '' || lname == '' || email == '' || pwd == '') {
                    $('#ass_err').html('Please provide all required fields');
                } // end if
                else {
                    $('#ass_err').html('');
                    var item = {
                        teacherid: teacherid,
                        groupid: groupid,
                        firstname: fname,
                        lastname: lname,
                        email: email,
                        pwd: pwd,
                        state: 'US'
                    };
                    var check_url = '/lms/custom/tutors/is_email_exists.php';
                    $.post(check_url, {email: email}).done(function (status) {
                        if (status > 0) {
                            $('#ass_err').html('Provided email already exists');
                            return false;
                        } // end if
                        else {
                            $('#ass_err').html('');
                            var url = '/lms/custom/common/add_new_assistant_done.php';
                            if (confirm('Add new assistance account to current class?')) {
                                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                                    console.log(data);
                                    $("[data-dismiss=modal]").trigger({type: "click"});
                                    //$('#myModal').data('modal', null);
                                    //document.location.reload();
                                }); // end of post
                            } // end if confirm
                        } // end else
                    }); // end of post
                } // end else
            }

            // ********** Send Message to graded students ***********

            if (event.target.id == 'select_all') {
                if ($('#select_all').prop('checked')) {
                    $('.students').prop('checked', true);
                } // end if
                else {
                    $('.students').prop('checked', false);
                } // end else
            }

            if (event.target.id == 'grades_get_send_message_dialog') {
                var userid = $('#userid').val();
                var selectedStudents = new Array();
                var n = $(".students:checked").length;
                if (n > 0) {
                    $(".students:checked").each(function () {
                        selectedStudents.push($(this).val());
                    });
                }
                var students = selectedStudents.join();
                console.log('Selected students: ' + students)
                if (students == '') {
                    alert('You did not select any student!')
                } // end if
                else {
                    var id = Math.round((new Date()).getTime() / 1000);
                    var url = '/lms/custom/common/grades_get_send_message_dialog.php';
                    var item = {userid: userid, id: id, emails: students};
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        var modalID = '#' + id;
                        $("body").append(data);
                        $(modalID).modal('show');
                    });
                }
            }


            if (event.target.id.indexOf("send_grade_comment_") >= 0) {
                var id = event.target.id.replace('send_grade_comment_', '');
                var userid = $('#userid').val();

                var subjectelid = '#subject_' + id;
                var subject = $(subjectelid).val();

                var msgelid = '#msg_' + id;
                var msg = $(msgelid).val();

                var emailelid = '#emails_' + id;
                var emails = $(emailelid).val();

                var errelid = '#send_grade_err_' + id;

                if (subject == '' || msg == '') {
                    $(errelid).html('Please provide message subject and content');
                } // end if
                else {
                    $(errelid).html('');
                    if (confirm('Send message to selected students?')) {
                        var url = '/lms/custom/common/send_grades_feedback.php';
                        var item = {teacherid: userid, emails: emails, subject: subject, msg: msg};
                        $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                            $("[data-dismiss=modal]").trigger({type: "click"});
                            console.log(data);
                        }); // end of post
                    }
                } // end else
            }

            // ***************** Share info section *****************

            if (event.target.id == 'share_info') {
                var userid = $('#userid').val();
                var id = Math.round((new Date()).getTime() / 1000);
                var url = '/lms/custom/common/get_share_info_dialog.php';
                var item = {userid: userid, id: id};
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    var modalID = '#' + id;
                    $("body").append(data);
                    $(modalID).modal('show');
                });
            }

            if (event.target.id.indexOf("send_share_info_") >= 0) {
                var id = event.target.id.replace('send_share_info_', '');
                var userid = $('#userid').val();
                var subelid = '#subject_' + id;
                var subject = $(subelid).val();
                var recipientelid = '#email_' + id;
                var recipient = $(recipientelid).val();
                var msgelid = '#msg_' + id;
                var msg = $(msgelid).val();
                var errlid = '#share_err_' + id;
                if (subject == '' || recipient == '' || msg == '') {
                    $(errlid).html('Please provide all required fields');
                    return false;
                } // end if
                else {
                    $(errlid).html('');
                    var url = '/lms/custom/common/send_share_info.php';
                    var item = {
                        userid: userid,
                        subject: subject,
                        recipient: recipient,
                        msg: msg
                    };
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log(data);
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        //$('#myModal').data('modal', null);
                    });
                }
            }


            // ***************** Export class grades section *****************

            if (event.target.id == 'export_class_grades') {
                var groupid = $('#teacher_groups').val();
                if (groupid > 0) {
                    var url = '/lms/custom/common/export_class_grades.php';
                    $.post(url, {groupid: groupid}).done(function (file) {
                        var url = 'https://www.newsfactsandanalysis.com/lms/custom/tutors/' + file;
                        window.open(url, '_blank');
                    });
                }
            }

            // ***************** Poll section *****************

            if (event.target.id == 'logout_dashboard') {
                if (confirm('Logout from the system?')) {
                    var url = '/lms/logout_dashboard.php';
                    $.post(url, {item: 1}).done(function () {
                        window.location = 'https://www.newsfactsandanalysis.com';
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
                        var item = {userid: userid, items: items, type: 1};
                        $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                            $('#poll_container').html(data);
                        });
                    }
                }
            }

            if (event.target.id == 'submit_quiz') {
                var items = [];
                var userid = $('#userid').val();
                var elid = '#total_items_2';
                var total = $(elid).val();
                console.log('Total items: ' + total);
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
                        var item = {userid: userid, items: items, type: 2};
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

                case 'logout':
                    if (confirm('Logout from the system?')) {
                        var url = '/lms/logout_dashboard.php';
                        $.post(url, {item: 1}).done(function () {
                            window.location = 'https://www.newsfactsandanalysis.com';
                        });
                    }
                    break;

            }

        });

    })
    ; // end of document ready


</script>

</body>
</html>





