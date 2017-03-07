
$(document).ready(function () {
    console.log("ready!");
    /**********************************************************************
     * 
     *  
     *                     Code related to survey
     *   
     *    
     **********************************************************************/

    $('#camps').DataTable();

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

    $("#add_camp").click(function () {
        console.log('Clicked ...');
        var title = $('#camp_title').val();
        var content = CKEDITOR.instances.editor1.getData();
        if (title != '' && content) {
            $('#camp_err').html('');
            if (confirm('Add new campaign?')) {
                var camp = {title: title, content: content};
                var url = 'http://globalizationplus.com/survey/add_camp.php';
                $.post(url, {camp: JSON.stringify(camp)}).done(function (data) {
                    $('#camp_container').html(data);
                }); // end of post
            } // end if confirm
        } // end if
        else {
            $('#camp_err').html('Please provide title and content');
        } // end else

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

    // Create classes data for students signup
    var request = {id: 1};
    var url = 'http://globalizationplus.com/lms/custom/students/create_typehead_data.php';
    $.post(url, request).done(function (data) {
        console.log(data);
    }); // end of post


    $.get('http://globalizationplus.com/lms/custom/students/groups.json', function (data) {
        $("#class").typeahead({source: data, items: 256000});
    });


    // Professors signup
    $("#prof_signup").submit(function (event) {
        event.preventDefault();
        var email = $('#email').val();
        var state = $('#state').val();
        var course1 = $('#course1').val();
        if (state > 0 && course1 != '') {
            var url = 'http://globalizationplus.com/lms/custom/tutors/is_email_exists.php';
            $.post(url, {email: email}).done(function (data) {
                if (data == 0) {
                    $('#form_err').html('');
                    $('#form_info').html('');
                    var course2 = $('#course2').val();
                    var course3 = $('#course3').val();
                    var course4 = $('#course4').val();
                    var course5 = $('#course5').val();
                    var course6 = $('#course6').val();

                    var url = 'http://globalizationplus.com/lms/custom/tutors/is_group_exists.php';
                    $.post(url, {groupname: course1}).done(function (data) {
                        if (data == 0) {
                            $('#form_err').html('');
                            $('#form_info').html('');
                            $('#ajax_loader').show();
                            var user = {
                                firstname: $('#first').val(),
                                lastname: $('#last').val(),
                                email: $('#email').val(),
                                pwd: $('#pwd').val(),
                                state: state,
                                street: $('#street').val(),
                                city: $('#city').val(),
                                zip: $('#zip').val(),
                                title: $('#title').val(),
                                school: $('#school').val(),
                                dep: $('#dep').val(),
                                site: 'Some Site',
                                course1: course1,
                                course2: course2,
                                course3: course3,
                                course4: course4,
                                course5: course5,
                                course6: course6
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
        } // end if state>0
        else {
            $('#form_err').html('Please select state and provide Course Name 1');
        } // end else
    });



    // Students signup
    $("#student_signup").submit(function (event) {
        event.preventDefault();
        var state = $('#state').val();
        var cardmonth = $('#cardmonth').val();
        var cardyear = $('#cardyear').val();
        var cardholder = $('#cardholder').val();
        $('#form_err').html('');
        $('#form_info').html('');
        if (state > 0 && cardmonth > 0 && cardyear > 0 && cardholder != '') {
            var groupname = $('#class').val();
            var email = $('#email').val();

            var clean_holder = cardholder.replace(/\s\s+/g, ' ');
            var names_arr = clean_holder.split(" ");

            var firstname, lastname;

            console.log('names array length: ' + names_arr.length);

            if (names_arr.length == 1) {
                $('#personal_payment_err').html('Please provide correct card holder name separated by space');
                return;
            }

            if (names_arr.length == 2) {
                console.log('Two names case ....');
                console.log('Holder name: ' + cardholder);
                firstname = names_arr[0];
                lastname = names_arr[1];
                console.log('Billing firstname: ' + firstname);
                console.log('Billing lastname: ' + lastname);
                if (typeof (firstname) === "undefined" || firstname == '' || typeof (lastname) === "undefined" || lastname == '') {
                    $('#personal_payment_err').html('Please provide correct card holder name separated by space');
                    return;
                }
            } // end if names_arr.length == 2

            if (names_arr.length == 3) {
                console.log('Three names case ...');
                console.log('Holder name: ' + cardholder);
                firstname = names_arr[0] + ' ' + names_arr[1];
                lastname = names_arr[2];
                console.log('Billing firstname: ' + firstname);
                console.log('Billing lastname: ' + lastname);
                if (typeof (firstname) === "undefined" || firstname == '' || typeof (lastname) === "undefined" || lastname == '') {
                    $('#personal_payment_err').html('Please provide correct card holder name separated by space');
                    return;
                }
            } // end if names_arr.length == 3


            $('#form_err').html('');
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
                                amount: 30,
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
                                cardholder: cardholder,
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
                document.location.reload();
            });
        }

    });
    // Adjust paid keys
    $('body').on('click', 'a.adjust', function () {
        console.log('Adjust subscription is clicked ....');
        var userid = $(this).data('userid');
        var groupid = $(this).data('groupid');
        var did = "#myModal_paid_" + userid;
        $(did).remove();
        $('.modal-backdrop').remove();
        var post_url = "http://globalizationplus.com/lms/utils/get_adjust_dialog.php";
        $.post(post_url, {userid: userid, groupid: groupid}).done(function (data) {
            $("body").append(data);
            $(did).modal('show');
            $("#subs_start").datepicker();
            $("#subs_exp").datepicker();
        });
    });
    // Adjust trial key
    $('body').on('click', 'a.trial_adjust', function () {
        console.log('Adjust trial key is clicked ....');
        var userid = $(this).data('userid');
        var groupid = $(this).data('groupid');
        var did = "#myModal_trial_" + userid;
        $(did).remove();
        $('.modal-backdrop').remove();
        var url = 'http://globalizationplus.com/lms/utils/get_adjust_trial_personal_key_modal_dialog.php';
        var user = {userid: userid, groupid: groupid};
        $.post(url, {user: JSON.stringify(user)}).done(function (data) {
            $("body").append(data);
            $(did).modal('show');
            $("#trial_start").datepicker();
            $("#trial_exp").datepicker();
        });
    });


    $('body').on('click', 'button', function (event) {

        if (event.target.id == 'modal_ok') {
            if (confirm('Adjust key expiration for current student?')) {
                var userid = $('#userid').val();
                var groupid = $('#groupid').val();
                var start = $('#subs_start').val();
                var exp = $('#subs_exp').val();
                var paymentid = $(this).data('paymentid');
                var post_url = "http://globalizationplus.com/lms/utils/adjust_subs.php";
                var subs = {userid: userid, groupid: groupid, start: start, exp: exp, paymentid: paymentid};
                $.post(post_url, {subs: JSON.stringify(subs)}).done(function (data) {
                    console.log(data);
                    $("[data-dismiss=modal]").trigger({type: "click"});
                    var url2 = 'http://globalizationplus.com/lms/utils/get_paid_keys.php';
                    $.post(url2, {item: 1}).done(function (data) {
                        $('#paid_keys').html(data);
                        $('#subs_table').DataTable();
                        $("#subs_start").datepicker();
                        $("#subs_exp").datepicker();
                    });
                });
            } // end if 
        }

        if (event.target.id == 'trial_ok') {
            var username = $('#trial_user').val();
            var groupname = $('#trial_class').val();
            if (username == '' || groupname == '') {
                $('#subs_err').html('Please select student and class');
            } // end if
            else {
                $('#subs_err').html('');
                if (confirm('Add trial key for current student?')) {
                    var post_url = "http://globalizationplus.com/lms/utils/add_trial_key.php";
                    $.post(post_url, {username: username, groupname: groupname}).done(function (data) {
                        console.log(data);
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $('#myModal').data('modal', null);
                    });
                } // end if 
            } // end else
        }

        if (event.target.id == 'personal_modal_trial_ok') {
            var userid = $('#userid').val();
            var groupid = $('#groupid').val();
            var start = $('#trial_start').val();
            var end = $('#trial_exp').val();

            if (start == '' || end == '') {
                $('#subs_err').html('Please provide key start and expiration dates');
            } // end if
            else {
                $('#subs_err').html('');
                if (confirm('Adjust trial key for selected user?')) {
                    var post_url = "http://globalizationplus.com/lms/utils/adjust_personal_trial_key.php";
                    var user = {userid: userid, groupid: groupid, start: start, end: end};
                    $.post(post_url, {user: JSON.stringify(user)}).done(function () {
                        //console.log(data);
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $('#myModal').data('modal', null);
                        var url = 'http://globalizationplus.com/lms/utils/get_trial_keys.php';
                        $.post(url, {item: 1}).done(function (data) {
                            $('#trial_keys').html(data);
                            $('#trial_table').DataTable();
                            $("#trial_start").datepicker();
                            $("#trial_exp").datepicker();
                        });
                    });
                } // end if
            } // end else
        }

        $("#target").click(function () {
            alert("Handler for .click() called.");
        });


        if (event.target.id == 'group_modal_trial_ok') {
            var users = $('#users').val();
            var start = $('#trial_start').val();
            var end = $('#trial_exp').val();
            if (start == '' || end == '') {
                $('#subs_err').html('Please select start and expiration dates(s)');
            } // end if 
            else {
                $('#subs_err').html('');
                if (confirm('Adjust trial key(s) for selected user(s)?')) {
                    var keys = {users: JSON.stringify(users), start: start, end: end};
                    var url = 'http://globalizationplus.com/lms/utils/adjust_group_trial_keys.php';
                    $.post(url, {users: JSON.stringify(keys)}).done(function () {
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $('#myModal').data('modal', null);
                        //document.location.reload();
                    });
                } // end if
            } // end else
        }


    }); // end of $('body').on('click', 'button'

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

    // Search trial
    $("#search_trial_button").click(function () {
        var item = $('#search_trial').val();
        if (item != '') {
            $('#ajax_trial').show();
            var url = 'http://globalizationplus.com/lms/utils/search_trial.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax_trial').hide();
                $('#trial_container').html(data);
            });
        } // end if item!=''
    });
    // Clear subs filter
    $("#clear_trial_button").click(function () {
        var url = 'http://globalizationplus.com/lms/utils/get_trial_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#trial_container').html(data);
        });
    });


    $("#add_trial_button").click(function () {
        console.log('Clicked ...');
        var url = 'http://globalizationplus.com/lms/utils/get_add_trial_key_dialog.php';
        $.post(url, {item: 1}).done(function (data) {
            $("body").append(data);
            $("#myModal").modal('show');

            $.get('/lms/utils/data/trial.json', function (data) {
                $("#trial_class").typeahead({source: data, items: 2400});
            }, 'json');

            $.get('/lms/utils/data/users.json', function (data) {
                $("#trial_user").typeahead({source: data, items: 2400});
            }, 'json');


            $("#subs_start").datepicker();
            $("#subs_exp").datepicker();
        });
    });

    $('#r').click(function () {
        console.log('Clicked ....');
        var url = 'http://globalizationplus.com/survey/get_queue.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#q').html(data);
        });
    });


    $('#adjust_trial_group').click(function () {
        var users = new Array();
        $("input[type=checkbox]:checked").each(function () {
            var user = {userid: $(this).data('userid'), groupid: $(this).data('groupid')};
            users.push(user);
        });

        if (users.length > 0) {
            var url = 'http://globalizationplus.com/lms/utils/get_trial_modal_dialog.php';
            $.post(url, {users: users}).done(function (data) {
                $("body").append(data);
                $("#myModal").modal('show');
                $("#trial_start").datepicker();
                $("#trial_exp").datepicker();
            });
        }
    });

    $('.trial_adjust').click(function () {
        var userid = $(this).data('userid');
        var groupid = $(this).data('groupid');
        var url = 'http://globalizationplus.com/lms/utils/get_adjust_trial_personal_key_modal_dialog.php';
        var user = {userid: userid, groupid: groupid};
        $.post(url, {user: JSON.stringify(user)}).done(function (data) {
            $("body").append(data);
            $("#myModal").modal('show');
            $("#trial_start").datepicker();
            $("#trial_exp").datepicker();
        });
    });


    $('body').on('click', function (event) {

        console.log('Event ID: ' + event.target.id);

        if (event.target.id == 'modal_cancel_trial') {
            console.log('Clicked ...');
            document.location.reload();
        }

        if (event.target.id.indexOf("cancel_trial_") >= 0) {
            document.location.reload();
        }


        if (event.target.id.indexOf("modal_cancel_paid_") >= 0) {
            document.location.reload();
        }


        if (event.target.id == 'logout_utils') {
            var url = 'http://globalizationplus.com/lms/utils/logout.php';
            if (confirm('Logout from system?')) {
                $.post(url, {item: 1}).done(function () {
                    window.location = 'http://globalizationplus.com/lms/utils';
                });
            } // end if confirm
        }


    }); // end of body click event


}); // end of document ready


