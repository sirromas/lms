$(document).ready(function () {
    console.log("ready!");
    /**********************************************************************
     *
     *
     *                     Code related to survey
     *
     *
     **********************************************************************/
    var iframeurl;
    $('#camps').DataTable();
    $('#progress_table').DataTable();

    // Update config 
    $("#update_config").click(function () {
        var username = $('#username').val();
        var password = $('#password').val();
        if (username != '' && password != '') {
            if (confirm('Update config data?')) {
                var config = {username: username, password: password};
                var request = {config: JSON.stringify(config)};
                var url = 'https://www.newsfactsandanalysis.com/survey/update_config.php';
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
            document.location = 'https://www.newsfactsandanalysis.com/survey/';
        }
    });

    function submit_login_form() {
        $('#login_form').submit();
    }

    function get_online_classes_table() {
        var url = '/lms/utils/get_online_classes.php';
        $.post(url, {id: 1}).done(function (data) {
            $('#oclasses').html(data);
            $('#online_classes_table').DataTable();
        });
    }

    function get_articles_table() {
        var url = '/lms/utils/get_articles_table.php';
        $.post(url, {id: 1}).done(function (data) {
            $('#publish').html(data);
            $('#archive_table').DataTable();
        });
    }

    function get_correct_answers_by_class(id) {
        var ca = [];
        var text;
        var ca_class = '.correct_answers' + id;
        $(ca_class).each(function (index) {
            if ($(this).is(':checked')) {
                text = 'Yes';
            } // end if
            else {
                text = 'No';
            }
            var item = {id: $(this).data('id'), text: text};
            ca.push(item);
        }); // end of each
        return ca;
    }

    function get_correct_answers_by_id(id, i) {
        var elid = '#ca_' + id + '_' + i;
        if ($(elid).is(':checked')) {
            text = 'Yes';
        } // end if
        else {
            text = 'No';
        }
        var item = {status: text};
        return item;
    }

    function get_question_answers(id) {
        var a = [];
        var a_class = '.answers' + id;
        $(a_class).each(function (index) {
            if ($(this).val() != '') {
                var ca = get_correct_answers_by_id(id, $(this).data('id'));
                var item = {id: $(this).data('id'), text: $(this).val(), ca: ca};
                a.push(item);
            }
        }); // end of each
        return a;
    }

    function show_loader() {
        console.log('Function called ..');
        $('#login_err').html('');
        //$('#after_form').show();
        document.getElementById("after_form").style.display = "block";
    }

    $('#login_form').submit(function () {
        setTimeout(show_loader, 1);
    });


    /*
     $("#submit_button").click(function () {
     $('#submit_button').attr("disabled", "disabled");
     $('#container27').hide();
     $('#after_form').show();
     submit_login_form();
     });
     */

    $("#grades").click(function () {
        console.log('Clicked ...');
    });

    // Process email sender form
    $("#launcher").submit(function (event) {
        event.preventDefault();
        var campaign = $('#campaigns_list').val();
        var email = $('#email').val();
        var fname = $('#fname').val();
        var lname = $('#lname').val();
        var file = $('#file')[0].files[0]
        console.log('File ' + JSON.stringify(file));
        if (campaign > 0) {
            if (email == '' && file == '') {
                $('#form_err').html('Please provide user data or file to be uploaded');
            } // end if 
            else {

                if (email != '' && fname != '' && lname != '') {
                    var item = {email: email, campid: campaign, firstname: fname, lastname: lname};
                    var request = {item: JSON.stringify(item)};
                    var url = 'https://www.newsfactsandanalysis.com/survey/launch.php';
                    $.post(url, request).done(function (data) {
                        $('#form_err').html(data);
                    });
                } // end if
                else {
                    $('#form_err').html('Please provide all required fields');
                }

                if (email == '' && file != '') {
                    var url = 'https://www.newsfactsandanalysis.com/survey/upload.php';
                    var file_data = $('#file').prop('files');
                    var form_data = new FormData();
                    form_data.append('campid', campaign);
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
                /*
                 if (email != '' && file != '') {
                 $('#form_err').html('You can upload file or provide recipient email, but not both');
                 }
                 */
            } // end else
        } // end if
        else {
            $('#form_err').html('Please select campaign to be sent');
        }
    });

    /**********************************************************************
     *
     *
     *                     Code related to LMS
     *
     *
     **********************************************************************/

    $("body").on("click mousedown mouseup focus blur keydown change", function (e) {
        //console.log(e);
    });

    // Monitor for Login errors reported from LMS
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    var code = getUrlParameter('errorcode');
    console.log('Error code: ' + code);
    if (code == 3) {
        $('#container27').hide();
        $('#login_err').html('Invalid email address or password');
    }

    $.get('https://www.newsfactsandanalysis.com/lms/custom/students/groups.json', function (data) {
        $("#class").typeahead({source: data, items: 256000});
    });


    // Professors signup
    $("#prof_signup").submit(function (event) {
        event.preventDefault();
        var email = $('#email').val();
        var state = $('#state').val();
        var course1 = $('#course1').val();
        if (state > 0 && course1 != '') {
            var url = 'https://www.newsfactsandanalysis.com/lms/custom/tutors/is_email_exists.php';
            $.post(url, {email: email}).done(function (data) {
                if (data == 0) {
                    $('#form_err').html('');
                    $('#form_info').html('');
                    var course2 = $('#course2').val();
                    var course3 = $('#course3').val();
                    var course4 = $('#course4').val();
                    var course5 = $('#course5').val();
                    var course6 = $('#course6').val();

                    var url = 'https://www.newsfactsandanalysis.com/lms/custom/tutors/is_group_exists.php';
                    $.post(url, {groupname: course1}).done(function (data) {
                        if (data == 0) {
                            $('#form_err').html('');
                            $('#form_info').html('');
                            $('#ajax_loader').show();
                            var user = {
                                firstname: $('#first').val(),
                                lastname: $('#last').val(),
                                email: $('#email').val(),
                                phone: $('#phone').val(),
                                pwd: 'strange12',
                                state: state,
                                street: $('#street').val(),
                                city: $('#city').val(),
                                zip: $('#zip').val(),
                                title: $('#title').val(),
                                school: $('#school').val(),
                                dep: $('#dep').val(),
                                site: $('#site').val(),
                                course1: course1,
                                course2: course2,
                                course3: course3,
                                course4: course4,
                                course5: course5,
                                course6: course6
                            };
                            var url = 'https://www.newsfactsandanalysis.com/lms/custom/tutors/signup.php';
                            $.post(url, {user: JSON.stringify(user)}).done(function (data) {
                                $('#ajax_loader').hide();
                                //$('#form_info').html(data);
                                //var url = 'https://newsfactsandanalysis.com/assets/images/thankyou/index.html';
                                var url = 'https://newsfactsandanalysis.com/registerthankyou.html';
                                window.location.href = url;
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
            var amount = $('#price').val();

            var groupname = $('#class').val();
            console.log('Class name: ' + groupname);
            var email = $('#email').val();

            var clean_holder = cardholder.replace(/\s\s+/g, ' ');
            var names_arr = clean_holder.split(" ");

            var firstname, lastname;

            console.log('names array length: ' + names_arr.length);

            if (names_arr.length == 1) {
                $('#form_err').html('Please provide correct card holder name separated by space');
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
                    $('#form_err').html('Please provide correct card holder name separated by space');
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
                    $('#form_err').html('Please provide correct card holder name separated by space');
                    return;
                }
            } // end if names_arr.length == 3


            $('#form_err').html('');
            var url = '/lms/custom/tutors/is_email_exists.php';
            $.post(url, {email: email}).done(function (data) {
                if (data == 0) {
                    $('#form_err').html('');
                    var url = '/lms/custom/tutors/is_group_exists.php';
                    $.post(url, {groupname: groupname}).done(function (data) {
                        if (data > 0) {
                            $('#ajax_loader').show();
                            var user = {
                                item: 'NewsFacts & Analysis - Tuition',
                                courseid: 2,
                                amount: amount,
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
                                class: groupname,
                                cardholder: cardholder,
                                cardnumber: $('#cardnumber').val(),
                                cvv: $('#cvv').val(),
                                cardmonth: $('#cardmonth').val(),
                                cardyear: $('#cardyear').val()
                            };
                            var url = 'https://www.newsfactsandanalysis.com/lms/custom/students/students_signup.php';
                            $.post(url, {user: JSON.stringify(user)}).done(function (data) {
                                $('#ajax_loader').hide();
                                //$('#form_info').html("<span style='color:black;'>" + data + "</span>");
                                var url = 'https://newsfactsandanalysis.com/registerthankyou.html';
                                window.location.href = url;
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


    $("#archieve_login").submit(function () {
        $('#ajax_loader').show();
    });

    // Student subscription payment 
    $("#student_payment").submit(function (event) {
        event.preventDefault();
        var holder = $('#holder').val();
        console.log('Holder: ' + holder);
        var holder_arr = holder.split(" ");
        var firstname = holder_arr[0];
        var lastname = holder_arr[1];

        if (firstname == '' || lastname == '') {
            $('#form_err').html('Please provide cardholder firstname and lastnemt separated by space');
            return;
        }

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
        var sum = 30;
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
            var user = {
                firstname: firstname,
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
                zip: zip
            };
            var url = "/lms/custom/students/students_prolong.php";
            $.post(url, {user: JSON.stringify(user)}).done(function (data) {
                $('#ajax_loader').hide();
                $('#form_info').html(data);
            }).error(function (data) {
                $('#ajax_loader').hide();
                $('#form_info').html("<p align='center'>Your subscription have been renewed</p>");
            }); // end of post
        } // end if group>0 && state>0 && cardmonth>0 && cardyear>0
    });


    // Professors confirmation
    $("#confirm").click(function () {
        $('#form_err').html('');
        $('#form_info').html('');
        var email = $('#email').val();
        var username = $('#username').val();
        var url = $('#page').val();
        if (url == '') {
            $('#form_err').html('Please provide your page URL');
        } // end if
        else {
            $('#form_err').html('');
            $('#form_info').html('');
            $('#ajax_loader').show();
            var user = {username: username, email: email, url: url};
            var post_url = "https://www.newsfactsandanalysis.com/lms/tutors/confirm.php";
            $.post(post_url, {user: JSON.stringify(user)}).done(function (data) {
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
            var post_url = "https://www.newsfactsandanalysis.com/lms/utils/confirm.php";
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
        var post_url = "https://www.newsfactsandanalysis.com/lms/utils/get_adjust_dialog.php";
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
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_adjust_trial_personal_key_modal_dialog.php';
        var user = {userid: userid, groupid: groupid};
        $.post(url, {user: JSON.stringify(user)}).done(function (data) {
            $("body").append(data);
            $(did).modal('show');
            $("#trial_start").datepicker();
            $("#trial_exp").datepicker();
        });
    });


    $('body').on('click', 'button', function (event) {

        if (event.target.id == 'add_poll') {
            var type = 1;
            var url = '/lms/utils/get_news_wizard.php';
            $.post(url, {type: type}).done(function (data) {
                $('#quiz').html(data);
                $.get('/lms/utils/data/articles.json', function (data) {
                    $("#article").typeahead({source: data, items: 256000});
                });
            });
        }

        if (event.target.id == 'add_quiz') {
            var type = 2;
            var url = '/lms/utils/get_news_wizard.php';
            $.post(url, {type: type}).done(function (data) {
                $('#quiz').html(data);
                $.get('/lms/utils/data/articles.json', function (data) {
                    $("#article").typeahead({source: data, items: 256000});
                });
            });

        }

        if (event.target.id == 'cancelQuiz') {
            var url = '/lms/utils/get_qiz_page.php';
            $.post(url, {type: 1}).done(function (data) {
                $('#quiz').html(data);
                $('#poll_table').DataTable();
            });
        }

        if (event.target.id == 'qnextStep2') {
            var title = $('#qtitle').val();
            var article = $('#article').val();
            var total = $('#q_total').val();
            var type = $('#type').val();
            if (title == '' || article == '') {
                $('#qStep1Error').html('Please provide title and select related article');
            } // end if
            else {
                $('#qStep1Error').html('');
                $("#qnextStep2").prop("disabled", true);
                var item = {title: title, article: article, total: total, type: type};
                var url = '/lms/utils/get_quiz_page_step2.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    $('#quiz').append(data);
                });
            }
        }


        if (event.target.id == 'add_new_quiz_item') {
            var questions = [];

            var title = $('#qtitle').val();
            var article = $('#article').val();
            var total = $('#q_total').val();
            var type = $('#type').val();

            if (title == '' || article == '') {
                $('#quiz_err').html('');
                $('#quiz_err').html('Please provide title and select related article');
                return false;
            }

            $(".questions").each(function (index) {
                var a = get_question_answers($(this).data('id'));
                if ($(this).val() != '' && a.length != 0) {
                    var item = {id: $(this).data('id'), text: $(this).val(), a: a};
                    questions.push(item);
                }
            }); // end of each
            // console.log('Questions array: ' + JSON.stringify(questions));

            if (questions.length == 0) {
                $('#quiz_err').html('');
                $('#quiz_err').html('Please provide questions text with answers');
                return false;
            }

            $('#quiz_err').html('');
            var item = {title: title, article: article, total: total, type: type, questions: questions};
            var url = '/lms/utils/add_new_quiz.php';
            $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                $('#quiz').html(data);
            });

        }


        if (event.target.id == 'modal_ok') {
            if (confirm('Adjust key expiration for current student?')) {
                var userid = $('#userid').val();
                var groupid = $('#groupid').val();
                var start = $('#subs_start').val();
                var exp = $('#subs_exp').val();
                var paymentid = $(this).data('paymentid');
                var post_url = "https://www.newsfactsandanalysis.com/lms/utils/adjust_subs.php";
                var subs = {userid: userid, groupid: groupid, start: start, exp: exp, paymentid: paymentid};
                $.post(post_url, {subs: JSON.stringify(subs)}).done(function (data) {
                    console.log(data);
                    $("[data-dismiss=modal]").trigger({type: "click"});
                    var url2 = 'https://www.newsfactsandanalysis.com/lms/utils/get_paid_keys.php';
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
                    var post_url = "https://www.newsfactsandanalysis.com/lms/utils/add_trial_key.php";
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
                    var post_url = "https://www.newsfactsandanalysis.com/lms/utils/adjust_personal_trial_key.php";
                    var user = {userid: userid, groupid: groupid, start: start, end: end};
                    $.post(post_url, {user: JSON.stringify(user)}).done(function () {
                        //console.log(data);
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $('#myModal').data('modal', null);
                        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_trial_keys.php';
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

        if (event.target.id == 'update_school_price') {
            var id = $('#id').val();
            var price = $('#school_price').val();
            if (price == '' || !$.isNumeric(price) || price == 0) {
                $('#price_err').html('Please provide valid price ');
            } // end if
            else {
                $('#price_err').html('');
                var item = {id: id, price: price};
                var url = 'https://www.newsfactsandanalysis.com/lms/utils/update_item_price.php';
                $.post(url, {item: JSON.stringify(item)}).done(function () {
                    $("[data-dismiss=modal]").trigger({type: "click"});
                    $('#myModal').data('modal', null);
                    document.location.reload();
                });
            }
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
                    var url = 'https://www.newsfactsandanalysis.com/lms/utils/adjust_group_trial_keys.php';
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
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/search_class.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax').hide();
                $('#classes_container').html(data);
            });
        } // end if item!=''
    });
    // Clear classes filter
    $("#clear_class_button").click(function () {
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_classes_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#classes_container').html(data);
        });
    });
    // Search tutors
    $("#search_tutor_button").click(function () {
        var item = $('#search_tutor').val();
        if (item != '') {
            $('#ajax_tutor').show();
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/search_tutor.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax_tutor').hide();
                $('#tutors_container').html(data);
            });
        } // end if item!=''
    });
    // Clear tutors filer
    $("#clear_tutor_button").click(function () {
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_tutors_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#tutors_container').html(data);
        });
    });
    // Search subs
    $("#search_subs_button").click(function () {
        var item = $('#search_subs').val();
        if (item != '') {
            $('#ajax_subs').show();
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/search_subs.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax_subs').hide();
                $('#subs_container').html(data);
            });
        } // end if item!=''
    });
    // Clear subs filter
    $("#clear_subs_button").click(function () {
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_subs_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#subs_container').html(data);
        });
    });

    // Search trial
    $("#search_trial_button").click(function () {
        var item = $('#search_trial').val();
        if (item != '') {
            $('#ajax_trial').show();
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/search_trial.php';
            $.post(url, {item: item}).done(function (data) {
                $('#ajax_trial').hide();
                $('#trial_container').html(data);
            });
        } // end if item!=''
    });
    // Clear subs filter
    $("#clear_trial_button").click(function () {
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_trial_page.php';
        $.post(url, {item: 1}).done(function (data) {
            $('#trial_container').html(data);
        });
    });


    $("#add_trial_button").click(function () {
        console.log('Clicked ...');
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_add_trial_key_dialog.php';
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
        var url = 'https://www.newsfactsandanalysis.com/survey/get_queue.php';
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
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_trial_modal_dialog.php';
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
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_adjust_trial_personal_key_modal_dialog.php';
        var user = {userid: userid, groupid: groupid};
        $.post(url, {user: JSON.stringify(user)}).done(function (data) {
            $("body").append(data);
            $("#myModal").modal('show');
            $("#trial_start").datepicker();
            $("#trial_exp").datepicker();
        });
    });

    $('.price_adjust').click(function () {
        var id = $(this).data('id');
        var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_adjust_price_modal_dialog.php';
        $.post(url, {id: id}).done(function (data) {
            $("body").append(data);
            $("#myModal").modal('show');
        });
    });

    $('.ar_item_del').click(function () {
        var id = $(this).data('id');
        if (confirm('Delete this article from archive?')) {
            var url = '/lms/utils/delete_archive_artricle.php';
            $.post(url, {id: id}).done(function (data) {
                get_articles_table();
            });
        }
    });


    $('body').on('click', function (event) {

        console.log('Event ID: ' + event.target.id);

        if (event.target.id == 'add_new_video_chat') {
            var title = $('#oclass_title').val();
            var group = $('#oclass_classes').val();
            var cdate = $('#oclass_date').val();
            if (title == '' || group == '' || cdate == '') {
                $('#oclass_err').html('Please provide all required fields');
            } // end if
            else {
                $('#oclass_err').html('');
                var item = {title: title, group: group, cdate: cdate};
                var url = '/lms/utils/add_new_online_class.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    get_online_classes_table();
                });
            } // end else
        }

        if (event.target.id.indexOf("del_online_class_") >= 0) {
            var id = event.target.id.replace('del_online_class_', '');
            if (confirm('Delete current class?')) {
                var url = '/lms/utils/delete_online_class.php';
                $.post(url, {id: id}).done(function (data) {
                    get_online_classes_table();
                });
            }
        }

        if (event.target.id == 'make_export') {
            var selected = [];
            var groupid = $('#tutor_groups').val();
            $('input[type=checkbox]').each(function () {
                if ($(this).is(":checked")) {
                    selected.push($(this).val());
                }
            }); // end foreach
            if (groupid == 0 || selected.length == 0) {
                $('#export_err').html('Please select class and items to be exported');
            } // end if
            else {
                $('#export_err').html('');
                var export_items = {groupid: groupid, items: selected.toString()};
                var url = '/lms/custom/tutors/create_export.php';
                $.post(url, {item: JSON.stringify(export_items)}).done(function (data) {
                    $('#export_links').html(data);
                });
            }

        }

        if (event.target.id == 'get_price_upload_dialog') {
            var url = '/lms/utils/get_upload_price_csv_modal_dialog.php';
            $.post(url, {id: 1}).done(function (data) {
                $("body").append(data);
                $("#myModal").modal('show');
            });
        }

        if (event.target.id == 'publish') {
            var file_data = $('#files').prop('files');
            var file = $('#files').val();
            var date1 = $('#a_date1').val();
            var date2 = $('#a_date2').val();
            var title = $('#title').val();
            if (file != '' && date1 != '' && date2 != '' && title != '') {
                $('#pub_err').html('');
                $('#ajax_loader').show();
                var file_data = $('#files').prop('files');
                var url = '/lms/utils/upload_article_file.php';
                var form_data = new FormData();
                $.each(file_data, function (key, value) {
                    form_data.append(key, value);
                });
                form_data.append('date1', date1);
                form_data.append('date2', date2);
                form_data.append('title', title);
                $('#loader').show();
                $.ajax({
                    url: url,
                    data: form_data,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        $('#ajax_loader').hide();
                        $('#pub_err').html(data);
                        $('#pub_err').css({"color": "black"});
                    } // end of success
                }); // end of $.ajax ..
            } // end if
            else {
                $('#pub_err').html('Please select file, provide news title and dates');
            }
        }

        if (event.target.id == 'upload_price_file') {
            var file = $('#price_scv').val();
            if (file == '') {
                $('#price_err').html('Please select CSV file to upload');
            } // end if
            else {
                $('#price_err').html('');
                var file_data = $('#price_scv').prop('files');
                var url = '/lms/utils/upload_price_csv_data.php';
                var form_data = new FormData();
                $.each(file_data, function (key, value) {
                    form_data.append(key, value);
                });
                $('#loader').show();
                $.ajax({
                    url: url,
                    data: form_data,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        console.log(data);
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $('#myModal').data('modal', null);
                        //document.location.reload();
                    } // end of success
                }); // end of $.ajax ..
            } // end else
        }

        if (event.target.id == 'upload_archive_ok') {
            var title = $('#title').val();
            var adate = $('#adate').val();
            var fileval = $('#uploadBtn').val();
            var file_data = $('#uploadBtn').prop('files');
            console.log('File data: ' + JSON.stringify(file_data));
            if (title == '' || adate == '' || fileval == '') {
                $('#archive_err').html('* required fields');
            } // end if
            else {
                $('#archive_err').html('');
                var url = '/lms/utils/upload_article.php';
                var form_data = new FormData();
                form_data.append('title', title);
                form_data.append('adate', adate);
                $.each(file_data, function (key, value) {
                    form_data.append(key, value);
                });
                $('#loader').show();
                $.ajax({
                    url: url,
                    data: form_data,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function () {
                        $("[data-dismiss=modal]").trigger({type: "click"});
                        $('#myModal').data('modal', null);
                        document.location.reload();
                    } // end of success
                }); // end of $.ajax ..
            } // end else
        }


        if (event.target.id == 'add_new_school_to_db') {
            var name = $('#name').val();
            var price = $('#price').val();
            if (name != '' && price != '' && $.isNumeric(price)) {
                $('#price_err').html('');
                var item = {name: name, price: price};
                var url = 'https://www.newsfactsandanalysis.com/lms/utils/add_new_school_to_db.php';
                $.post(url, {item: JSON.stringify(item)}).done(function () {
                    $("[data-dismiss=modal]").trigger({type: "click"});
                    $('#myModal').data('modal', null);
                    document.location.reload();
                });
            } // end if
            else {
                $('#price_err').html('Please provide schoolname and its price');
            } // end else
        }

        if (event.target.id == 'add_new_school') {
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_add_new_school_modal_dialog.php';
            $.post(url, {id: id}).done(function (data) {
                $("body").append(data);
                $("#myModal").modal('show');
            });
        }

        if (event.target.id == 'modal_cancel_trial') {
            console.log('Clicked ...');
            document.location.reload();
        }

        if (event.target.id == 'add_q') {
            var num = $('#camp_q_num').val();
            if (num > 0) {
                var url = 'https://www.newsfactsandanalysis.com/survey/add_question.php';
                $.post(url, {num: num}).done(function (data) {
                    $('#q_container').html(data);
                });
            }
        }

        if (event.target.id == 'update_camp') {
            var q;
            var q_num = $('#q_num').val();
            var questions = [];
            var campid = $('#campid').val();
            var msg = CKEDITOR.instances.editor1.getData();
            for (var i = 1; i <= q_num; i++) {
                var answer = [];
                var elid = '#q_edit_' + i;
                var qtext = $(elid).val();
                var qid = $(elid).data('id');
                var a_class = '.q_a_' + qid;
                $(a_class).each(function () {
                    if ($(this).val() != '') {
                        var aid = $(this).data('id');
                        var a = {aid: aid, text: $(this).val()};
                        answer.push(a);
                    } // end if 
                }); // end each
                q = {id: qid, text: qtext, a: answer};
                questions.push(q);
            }
            if (confirm('Update current survey')) {
                var campaign = {id: campid, msg: msg, q: JSON.stringify(questions)};
                var url = 'https://www.newsfactsandanalysis.com/survey/update_camp.php';
                $.post(url, {camp: JSON.stringify(campaign)}).done(function (data) {
                    console.log(data);
                    document.location.reload();
                });
            }
        }

        function get_reply_items(i) {
            var replies = [];
            for (var k = 1; k <= 6; k++) {
                var reply_id = '#r_' + k + '_' + i;
                var color_id = '#cpicker_' + k + '_' + i;
                var text = $(reply_id).val();
                var color = $(color_id).val();
                if (text != '') {
                    var reply = {text: text, color: color};
                    replies.push(reply);
                } // end if 
            } // end for
            return replies;
        }

        if (event.target.id == 'add_camp') {
            var questions = [];
            var from = $('#from').val();
            var subject = $('#subject').val();
            var title = $('#camp_title').val();
            var total = $('#camp_q_num').val();
            var content = CKEDITOR.instances.editor1.getData();
            if (title != '' && content != '' && from != '' && subject != '') {
                $('#camp_err').html('');
                for (var i = 1; i <= total; i++) {
                    var qid = '#q_text_' + i;
                    var qtext = $(qid).val();
                    if (qtext != '') {
                        var replies = get_reply_items(i);
                        var q = {qtext: qtext, replies: replies};
                        questions.push(q);
                    } // end if qtext != ''
                } // end for
                $('#camp_err').html('');
                if (confirm('Add new campaign?')) {
                    var camp = {
                        from: from,
                        subject: subject,
                        title: title,
                        content: content,
                        questions: questions
                    };
                    var url = 'https://www.newsfactsandanalysis.com/survey/add_camp.php';
                    $.post(url, {camp: JSON.stringify(camp)}).done(function (data) {
                        console.log(data);
                        document.location.reload();
                    }); // end of post
                } // end if confirm
            } // end if title != '' && content
            else {
                $('#camp_err').html('Please provide all required fields');
            } // end else
        }

        if (event.target.id.indexOf("camp_edit_") >= 0) {
            var id = event.target.id.replace('camp_edit_', '');
            var url = 'https://www.newsfactsandanalysis.com/survey/edit_survey.php';
            $.post(url, {id: id}).done(function (data) {
                $('#camp').html(data);
            });
        }

        if (event.target.id.indexOf("camp_del_") >= 0) {
            if (confirm('Delete current campaign?')) {
                var id = event.target.id.replace('camp_del_', '');
                var url = 'https://www.newsfactsandanalysis.com/survey/del_survey.php';
                $.post(url, {id: id}).done(function (data) {
                    console.log(data);
                    document.location.reload();
                });
            }
        }

        if (event.target.id.indexOf("cancel_trial_") >= 0) {
            document.location.reload();
        }


        if (event.target.id.indexOf("modal_cancel_paid_") >= 0) {
            document.location.reload();
        }

        if (event.target.id.indexOf("camp_preview_") >= 0) {
            var id = event.target.id.replace('camp_preview_', '');
            var url = 'https://www.newsfactsandanalysis.com/survey/preview_camp.php';
            $.post(url, {id: id}).done(function (data) {
                $('#camp').html(data);
            });
        }

        if (event.target.id == 'back_camp') {
            var url = 'https://www.newsfactsandanalysis.com/survey/get_survey_tab.php';
            $.post(url, {id: 1}).done(function (data) {
                $('#camp').html(data);
                $('#camps').DataTable();
            });

        }


        if (event.target.id == 'logout_utils') {
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/logout.php';
            if (confirm('Logout from system?')) {
                $.post(url, {item: 1}).done(function () {
                    window.location = 'http://www.newsfactsandanalysis.com/lms/utils';
                });
            } // end if confirm
        }

        if (event.target.id == 'logout_account_archive') {
            var url = 'https://www.newsfactsandanalysis.com/lms/archive/logout.php';
            if (confirm('Logout from system?')) {
                $.post(url, {item: 1}).done(function () {
                    window.location = 'http://www.newsfactsandanalysis.com/lms/archive';
                });
            } // end if confirm
        }

        if (event.target.id == 'update_template') {
            var id = $('#template_id').val();
            var content = CKEDITOR.instances.editor1.getData();
            var template = {id: id, content: content};
            if (confirm('Update current template?')) {
                var url = 'https://www.newsfactsandanalysis.com/lms/utils/update_template.php';
                $.post(url, {template: JSON.stringify(template)}).done(function () {
                    document.location.reload();
                });
            } // end if confirm
        }


        if (event.target.id.indexOf("upload_img_") >= 0) {
            var id = event.target.id.replace('upload_img_', '');
            var elid = '#a_img_' + id;
            var msg_elid = '#upload_msg_' + id;
            var file_data = $(elid).prop('files');
            var url = 'https://www.newsfactsandanalysis.com/survey/upload_img.php';
            var form_data = new FormData();
            form_data.append('a_id', id);
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
                    $(msg_elid).html(data);
                } // end of success
            }); // end of $.ajax ..
        }

        if (event.target.id.indexOf("del_img_") >= 0) {
            var id = event.target.id.replace('del_img_', '');
            if (confirm('Delete current image?')) {
                var url = 'https://www.newsfactsandanalysis.com/survey/del_img.php';
                $.post(url, {id: id}).done(function () {
                    document.location.reload();
                });
            } // end if confirm
        }


        if (event.target.id.indexOf("camp_status_") >= 0) {
            var text;
            var id = event.target.id.replace('camp_status_', '');
            var elid = '#' + event.target.id;
            var status = $(elid).data('status');

            if (status == '0') {
                text = 'Enable survey processing?';
            } // end if
            else {
                text = 'Disable survey processing?';
            } // end else

            var camp = {id: id, status: status};
            var url = 'https://www.newsfactsandanalysis.com/survey/change_camp_status.php';
            if (confirm(text)) {
                $.post(url, {camp: JSON.stringify(camp)}).done(function () {
                    document.location.reload();
                });
            }
        }

        if (event.target.id.indexOf("camp_refresh_") >= 0) {
            var id = event.target.id.replace('camp_refresh_', '');
            var divelid = '#progress_div_' + id;
            $(divelid).fadeTo('fast', 0.33);
            var url = 'https://www.newsfactsandanalysis.com/survey/update_camp_progress.php';
            $.post(url, {id: id}).done(function (data) {
                $(divelid).html(data);
                $(divelid).fadeTo('fast', 1);
            });
        }


        if (event.target.id == 'add_forum') {
            var url = '/lms/utils/add_forum.php';
            $.post(url, {id: 1}).done(function (data) {
                $('#forum').html(data);
                $.get('/lms/utils/data/articles.json', function (data) {
                    $("#article").typeahead({source: data, items: 256000});
                });
            });
        }

        if (event.target.id == 'cancelForum') {
            var url = '/lms/utils/get_forum_page.php';
            $.post(url, {id: 1}).done(function (data) {
                $('#forum').html(data);
                $('#forum_table').DataTable();
            });
        }

        if (event.target.id == 'add_forum_done') {
            var title = $('#ftitle').val();
            var article = $('#article').val();
            if (title == '' || article == '') {
                $('#forum_err').html('Please provide forum title and select related article');
                return false;
            } // end if
            else {
                $('#forum_err').html('');
                var item = {article: article, title: title};
                var url = '/lms/utils/add_forum_done.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    $('#forum').html(data);
                });
            }
        }


    }); // end of body click event

    $('body').on('change', function (event) {


        if (event.target.id == 'templates_list') {
            var elid = '#' + event.target.id;
            var id = $(elid).val();
            var url = 'https://www.newsfactsandanalysis.com/lms/utils/get_email_template.php';
            $.post(url, {id: id}).done(function (data) {
                $('#template_content').html(data);
            });
        }


        if (event.target.id == 'res_campaigns_list') {
            $('#res_loader').show();
            var chart_data = [];
            var campid = $('#res_campaigns_list').val();
            var url = 'https://www.newsfactsandanalysis.com/survey/get_campaign_results.php';
            $.post(url, {id: campid}).done(function (data) {
                $('#res_loader').hide();
                $("#camp_result").html('');
                $res = $.parseJSON(data);
                $.each($res, function (index, obj) {
                    var qid = obj.qid;
                    var table = obj.table;
                    var stat = obj.stat;
                    $("#camp_result").append($(table));
                    $.each(JSON.parse(stat), function (index, value) {
                        var item = String(value).split('@');
                        var item_arr = [item[0], parseInt(item[1])];
                        chart_data.push(item_arr);
                    }); // end of each
                    console.log('Charts data array: ' + chart_data);
                    var container = 'q_chart_' + qid;
                    Highcharts.chart(container, {
                        chart: {
                            type: 'pie',
                            options3d: {
                                enabled: true,
                                alpha: 45,
                                beta: 0
                            }
                        },
                        title: {
                            text: 'Survey results'
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                depth: 35,
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.name}'
                                }
                            }
                        },
                        series: [{
                            type: 'pie',
                            name: 'Hits',
                            data: chart_data
                        }]
                    });
                }); // end of each

            }); // end of post
        }

        if (event.target.id == 'camp_q_num') {
            var num = $('#camp_q_num').val();
            var url = 'https://www.newsfactsandanalysis.com/survey/get_add_camp_questions_block.php';
            $.post(url, {num: num}).done(function (data) {
                $('#q_container').html(data);
                $('#button_container').show();
            });
        }

        if (event.target.id == 'school') {
            var url = 'https://www.newsfactsandanalysis.com/lms/custom/students/get_school_price.php';
            var name = $('#school').val();
            $.post(url, {name: name}).done(function (price) {
                if ($.isNumeric(price)) {
                    $('#price').val(price); // hidden value;
                    $('#ui_price').html(price); // visible for students
                }
            });
        }

    }); // end of body change event


    /************************************************************************
     *
     *                             Tutors grades page
     *
     ************************************************************************/

    var dictionary_url = 'https://www.newsfactsandanalysis.com/lms/dictionary/index.php';

    $('.dictionary').click(function () {
        $('#ext_container').hide();
        $('#page').attr('src', dictionary_url);
        $('#page').height($('#page').contents().height());
        $('#page').width($('#page').contents().width());
        $('#body').show();
    });

    $('.ar').click(function () {
        $('#header_img').show();
        $('#body').hide();
        var url = '/lms/custom/tutors/get_archive_page.php';
        $.post(url, {num: 1}).done(function (data) {
            $('#ext_container').html(data);
            $('#ext_container').show();
        });
    });

    $('.gr').click(function () {
        $('#header_img').show();
        $('#body').hide();
        var userid = $('#userid').val();
        var url = '/lms/custom/tutors/get_grades_page.php';
        $.post(url, {userid: userid}).done(function (data) {
            $('#ext_container').html(data);
            $('#ext_container').show();
        });
    });

    $('.ex').click(function () {
        $('#header_img').show();
        $('#body').hide();
        var userid = $('#userid').val();
        console.log('User ID: ' + userid);
        var url = '/lms/custom/tutors/get_export_page.php';
        $.post(url, {userid: userid}).done(function (data) {
            $('#ext_container').html(data);
            $('#ext_container').show();
        });
    });


    $('#ajax_upload_file').click(function () {
        var filname = $('#uploadBtn').val();
        if (filname == '') {
            $('#upload_err').html('Please select file');
        } // end if
        else {
            $('#upload_err').html('');
            var title = $('#title').val();
            var url = '/lms/utils/upload_article.php';
            var file_data = $('#uploadBtn').prop('files');
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
        } // end else

    });

    $('#article_upload_dialog').click(function () {
        var url = '/lms/utils/get_upload_archive_modal_dialog.php';
        $.post(url, {id: 1}).done(function (data) {
            $("body").append(data);
            $("#myModal").modal('show');
            $("#adate").datepicker();
        });
    });


    /************************************************************************
     *
     *                       Students  page
     *
     ************************************************************************/


    function make_base_auth(user, password) {
        var tok = user + ':' + password;
        var hash = btoa(tok);
        return "Basic " + hash;
    }


}); // end of document ready


