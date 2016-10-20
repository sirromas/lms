
$(document).ready(function () {
    console.log("ready!");

    /**********************************************************************
     * 
     *  
     *                     Code related to survey
     *   
     *    
     **********************************************************************/

    // Update config 
    $("#update_config").click(function () {
        var smtp_host = $('#smtp_host').val();
        var smtp_port = $('#smtp_port').val();
        var smtp_user = $('#smtp_user').val();
        var smtp_password = $('#smtp_password').val();
        var username = $('#username').val();
        var password = $('#password').val();
        if (smtp_host != '' && smtp_port != '' && smtp_user != '' && username != '' && password != '' && smtp_password != '') {
            if (confirm('Update config data?')) {
                var config = {smtp_host: smtp_host,
                    smtp_port: smtp_port,
                    smtp_user: smtp_user,
                    smtp_password: smtp_password,
                    username: username,
                    password: password};
                var request = {config: JSON.stringify(config)};
                var url = 'http://globalizationplus.com/survey/update_config.php';
                $.post(url, request).done(function (data) {
                    $('#config_err').html(data);
                }); // end of post
            } // end if confirm
        } // end if  
        else {
            $('#config_err').html('Please provide non-empty config values');
        }
    });
    $("#logout").click(function () {
        if (confirm('Logout from the system?')) {
            document.location = 'http://globalizationplus.com/survey/';
        }
    });

    // Process email sender form
    $("#launcher").submit(function (event) {
        event.preventDefault();
        var email = $('#email').val();
        var file = $('#file').val();
        if (email == '' && file == '') {
            $('#form_err').html('Please provide email or file to be uploaded');
        } // end if 
        else {
            if (email != '' && file == '') {
                var request = {email: email};
                var url = 'http://globalizationplus.com/survey/launch.php';
                $.post(url, request).done(function (data) {
                    $('#form_err').html(data);
                });
            }

            if (email == '' && file != '') {
                var url = 'http://globalizationplus.com/survey/upload.php';
                var file_data = $('#file').prop('files');
                var form_data = new FormData();
                $.each(file_data, function (key, value) {
                    form_data.append(key, value);
                });
                $.ajax({
                    url: url,
                    data: form_data,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        $('#form_err').html(data);
                    } // end of success
                }); // end of $.ajax ..
            }
            if (email != '' && file != '') {
                $('#form_err').html('You can upload file or provide recipient email, but not both');
            }
        } // end else
    });

    /**********************************************************************
     * 
     *  
     *                     Code related to LMS
     *   
     *    
     **********************************************************************/

    // Professors signup
    $("#prof_signup").submit(function (event) {
        event.preventDefault();
        var email = $('#email').val();
        var url = 'http://globalizationplus.com/lms/custom/tutors/is_email_exists.php';
        $.post(url, {email: email}).done(function (data) {
            if (data == 0) {
                $('#form_err').html('');
                $('#form_info').html('');
                var groupname = $('#class').val();
                var url = 'http://globalizationplus.com/lms/custom/tutors/is_group_exists.php';
                $.post(url, {groupname: groupname}).done(function (data) {
                    if (data == 0) {
                        $('#form_err').html('');
                        $('#form_info').html('');
                        $('#ajax_loader').show();
                        var user = {
                            firstname: $('#first').val(),
                            lastname: $('#last').val(),
                            email: $('#email').val(),
                            pwd: $('#pwd').val(),
                            street: $('#street').val(),
                            city: $('#city').val(),
                            zip: $('#zip').val(),
                            title: $('#title').val(),
                            school: $('#school').val(),
                            dep: $('#dep').val(),
                            site: $('#site').val(),
                            class: $('#class').val()
                        };
                        var url = 'http://globalizationplus.com/lms/custom/tutors/signup.php';
                        $.post(url, {user: JSON.stringify(user)}).done(function (data) {
                            $('#ajax_loader').hide();
                            $('#form_info').html(data);
                        });
                    } // end if data==0
                    else {
                        $('#form_info').html('');
                        $('#form_err').html('Class name already exists');
                    }
                }); // end of post
            } // end if data==0
            else {
                $('#form_info').html('');
                $('#form_err').html('Provided email already exists');
            } // end else 
        }); // end of post
    });

    // Students signup
    $("#student_signup").submit(function (event) {
        event.preventDefault();
        var state = $('#state').val();
        var cardmonth = $('#cardmonth').val();
        var cardyear = $('#cardyear').val();
        $('#form_err').html('');
        $('#form_info').html('');
        if (state > 0 && cardmonth > 0 && cardyear > 0) {
            var groupname = $('#class').val();
            var email = $('#email').val();
            var url = 'http://globalizationplus.com/lms/custom/tutors/is_email_exists.php';
            $.post(url, {email: email}).done(function (data) {
                if (data == 0) {
                    $('#form_err').html('');
                    var url = 'http://globalizationplus.com/lms/custom/tutors/is_group_exists.php';
                    $.post(url, {groupname: groupname}).done(function (data) {
                        if (data > 0) {
                            $('#ajax_loader').show();
                            var user = {
                                item: 'Globalization Plus - Tuition',
                                courseid: 2,
                                amount: 27,
                                firstname: $('#first').val(),
                                lastname: $('#last').val(),
                                email: $('#email').val(),
                                pwd: $('#pwd').val(),
                                street: $('#street').val(),
                                city: $('#city').val(),
                                state: $('#state').val(),
                                zip: $('#zip').val(),
                                school: $('#school').val(),
                                title: 'Student',
                                dep: 'n/a',
                                site: 'n/a',
                                class: $('#class').val(),
                                cardnumber: $('#cardnumber').val(),
                                cvv: $('#cvv').val(),
                                cardmonth: $('#cardmonth').val(),
                                cardyear: $('#cardyear').val()
                            };
                            var url = 'http://globalizationplus.com/lms/custom/students/students_signup.php';
                            $.post(url, {user: JSON.stringify(user)}).done(function (data) {
                                $('#ajax_loader').hide();
                                $('#form_info').html("<span style='color:black;'>" + data + "</span>");
                            }); // end of post
                        } // end if data>0 (group exists)
                        else {
                            $('#form_err').html('Class does not exist');
                        } // end else
                    }); // end of group post
                } // end if data==0 (email is not in use)
                else {
                    $('#form_err').html('This email is already in use');
                } // end else
            }); // end of email post
        } // end if all form inputs are ok
        else {
            $('#form_err').html('Please provide all required fields');
        }
    });

    // Student subscription payment 
    $("#student_payment").submit(function (event) {
        event.preventDefault();
        var holder = $('#holder').val();
        var holder_arr = holder.split(" ");
        var firstname = holder_arr[0];
        var lastname = holder_arr[1];
        var userid = $('#holder').data('userid');
        var address = $('#address').val();
        var city = $('#city').val();
        var zip = $('#zip').val();
        var state = $('#state').val();
        var cardnumber = $('#cardnumber').val();
        var cvv = $('#cvv').val();
        var cardmonth = $('#cardmonth').val();
        var cardyear = $('#cardyear').val();
        var group = $('#class').val();
        var sum = $('#amount').val();

        if (group == 0) {
            $('#form_err').html('Please select class');
            return;
        }
        if (state == 0) {
            $('#form_err').html('Please select state');
            return;
        }
        if (cardmonth == 0 || cardyear == 0) {
            $('#form_err').html('Please put expiration date');
            return;
        }

        if (group > 0 && state != 0 && cardmonth > 0 && cardyear > 0) {
            $('#form_err').html('');
            $('#ajax_loader').show();
            var user = {firstname: firstname,
                lastname: lastname,
                userid: userid,
                cardnumber: cardnumber,
                cvv: cvv,
                cardmonth: cardmonth,
                cardyear: cardyear,
                amount: sum,
                item: 'Subscription payment',
                class: group,
                street: address,
                state: state,
                city: city,
                zip: zip};
            var url = "http://globalizationplus.com/lms/custom/students/students_prolong.php";
            $.post(url, {user: JSON.stringify(user)}).done(function (data) {
                $('#ajax_loader').hide();
                $('#form_info').html(data);
            }); // end of post
        } // end if group>0 && state>0 && cardmonth>0 && cardyear>0
    });

    // Professors confirmation
    $("#confirm").click(function () {
        $('#form_err').html('');
        $('#form_info').html('');
        var email = $('#email').val();
        var url = $('#page').val();
        if (url == '') {
            $('#form_err').html('Please provide your page URL');
        } // end if
        else {
            $('#form_err').html('');
            $('#form_info').html('');
            $('#ajax_loader').show();
            var post_url = "http://globalizationplus.com/lms/tutors/confirm.php";
            $.post(post_url, {email: email, url: url}).done(function (data) {
                $('#ajax_loader').hide();
                $('#form_info').html(data);
            }).fail(function () {
                $('#form_info').html('Error happened ...');
            }); // end of post
        } // end else
    });

    $('body').on('click', 'a.confirm', function () {
        var userid = $(this).data('userid');
        if (confirm('Confirm current processor?')) {
            var post_url = "http://globalizationplus.com/lms/utils/confirm.php";
            $.post(post_url, {userid: userid}).done(function () {
                var url = 'http://globalizationplus.com/lms/utils/get_tutors_page.php';
                $.post(url, {id: 1}).done(function (data) {
                    $('#tutors_container').html(data);
                });
            });
        }

    });

    // Adjust paid keys
    $('body').on('click', 'a.adjust', function () {
        var userid = $(this).data('userid');
        var groupid = $(this).data('groupid');
        console.log('User ID: ' + userid);
        console.log('Group ID: ' + groupid);

        /*
         var post_url = "http://globalizationplus.com/lms/utils/get_jey_detailes.php";
         $.post(post_url, {userid: userid}).done(function () {
         document.location.reload();
         });
         */
    });

    // Search classes
    $("#search_class_button").click(function () {
        var item = $('#search_class').val();
        if (item != '') {
            $('#ajax').show();
            var url = 'http://globalizationplus.com/lms/utils/search_class.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax').hide();
                $('#classes_container').html(data);
            });
        } // end if item!=''
    });

    // Clear classes filter
    $("#clear_class_button").click(function () {
        var url = 'http://globalizationplus.com/lms/utils/get_classes_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#classes_container').html(data);
        });
    });

    // Search tutors
    $("#search_tutor_button").click(function () {
        var item = $('#search_tutor').val();
        if (item != '') {
            $('#ajax_tutor').show();
            var url = 'http://globalizationplus.com/lms/utils/search_tutor.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax_tutor').hide();
                $('#tutors_container').html(data);
            });
        } // end if item!=''
    });

    // Clear tutors filer
    $("#clear_tutor_button").click(function () {
        var url = 'http://globalizationplus.com/lms/utils/get_tutors_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#tutors_container').html(data);
        });
    });

    // Search subs
    $("#search_subs_button").click(function () {
        var item = $('#search_subs').val();
        if (item != '') {
            $('#ajax_subs').show();
            var url = 'http://globalizationplus.com/lms/utils/search_subs.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax_subs').hide();
                $('#subs_container').html(data);
            });
        } // end if item!=''
    });

    // Clear subs filter
    $("#clear_subs_button").click(function () {
        var url = 'http://globalizationplus.com/lms/utils/get_subs_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#subs_container').html(data);
        });
    });

}); // end of document ready


