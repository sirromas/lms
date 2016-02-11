/*******************************************************************************
 * 
 * Code related to signup process
 * 
 ******************************************************************************/

$(document).ready(function () {

    $('#a_student').on('click', function () {
        console.log("Student signup clicked");
        $.post("lms/update_groups.php", function (data) {
            console.log('Groups updated: ' + data);
        });
        $('#signupwrap_professor').hide();
        $('#signupwrap_student').show();
    });

    $("#a_tutor").click(function () {
        console.log("Tutor signup clicked");
        $('#signupwrap_professor').show();
        $('#signupwrap_student').hide();
    });

    /************************************************************************
     * 
     *  
     *                   Student's form
     *   
     * 
     ************************************************************************/


    // ************** Student Form change event **************        
    $('#signupform_student').change(function () {

        $("#fn_err").html('');
        $("#ln_err").html('');
        $("#email_err").html('');
        $("#pwd_err").html('');
        $("#group_err").html('');
        $("#school_err").html('');
        $("#addr_err").html('');

    });

    // **************Student Form submit event **************
    $('#signupform_student').submit(function (event) {

        event.preventDefault();

        var fn = $('#firstname').val();
        if (fn == '') {
            $("#fn_err").html('Please provide firstname');
            return false;
        }
        var ln = $('#lastname').val();
        if (ln == '') {
            $("#ln_err").html('Please provide lastname');
            return false;
        }

        var email = $('#email').val();
        if (email == '') {
            $("#email_err").html('Please provide email');
            return false;
        }

        if (validateEmail(email) != true) {
            $("#email_err").html('Provided email is incorrect');
            return false;
        }

        var password = $('#password').val();
        if (password.length == 0 || password.length <= 5) {
            $("#pwd_err").html('Please provide password at least 5 symbols');
            return false;
        }

        var address = $('#address').val();
        if (address == '') {
            $("#addr_err").html('Please provide address');
            return false;
        }

        var school = $("#school").val();
        if (school == '') {
            $("#school_err").html('Please provide school name');
            return false;
        }

        var groups = $('#group').val();
        if (groups == '') {
            $("#group_err").html('Please select course');
            return false;
        }

        if (fn != '' && ln != '' && email != '' && validateEmail(email) == true && password != '' && address != '' && school != '' && groups != '') {
            console.log('Inside if when all data are provided ...');
            $("#group_err").html('');
            // Check is email already in use?
            $.post("lms/getGroups.php", {
                email: email
            }).done(function (data) {
                console.log('Response (email exists): ' + data);
                if (data > 0) {
                    $("#email_err").html('Provided email already in use');
                    return false;
                }
                else {
                    var courses = 3; // We have only one course
                    // If email is not used we could proceed with signup
                    var url = 'http://globalizationplus.com/lms/login/signup.php';
                    var user_type = 'student';
                    query = {user_type: user_type,
                        firstname: fn,
                        lastname: ln,
                        username: email,
                        password: password,
                        email: email,
                        school: school,
                        course: courses,
                        group: groups,
                        address: address};

                    $('#signupwrap_student').fadeTo("slow", 0.33);
                    $.post(url, query).done(function (data) {
                        console.log('Server response: ' + data);
                        $('#signupwrap_student').fadeTo("slow", 1);
                        $('#signupwrap_student').height(30);
                        $("#signupwrap_student").html(data);
                        return false;
                    }).fail(function (data) {
                        console.log('Fail data: ' + data);
                        $("#signupwrap_student").html('Ops something wrong ...');
                        return false;
                    }); // end fail
                    return false;
                } // end else when email is not used
            }); // end of done(function (data) 
        } // end if fn!='' && ln!='' && email!=''
    }); // end of $('#signupform_student').submit(function ()

    /************************************************************************
     * 
     *  
     *                         Professor's form
     *   
     * 
     ************************************************************************/

    // ************** Professor Form change event **************
    $('#signupform_prof').change(function () {

        $("#fn_err").html('');
        $("#ln_err").html('');
        $("#email_err").html('');
        $("#pwd_err").html('');
        $("#new_group_err").html('');
        $("#title_err").html('');
        $("#department_err").html('');
        $("#school_err").html('');
        $("#addr_err").html('');

    }); // end of '#signupform_prof'.change(function () 


    // **************Professor Form submit event **************
    $('#signupform_prof').submit(function (event) {

        event.preventDefault();

        var fn = $('#firstname_prof').val();
        if (fn == '') {
            $("#fn_err").html('Please provide firstname');
            return false;
        }

        var ln = $('#lastname_prof').val();
        if (ln == '') {
            $("#ln_err").html('Please provide lastname');
            return false;
        }

        var email = $('#email_prof').val();
        if (email == '') {
            $("#email_err").html('Please provide email');
            return false;
        }

        if (validateEmail(email) != true) {
            $("#email_err").html('Provided email is incorrect');
            return false;
        }


        var password = $('#password_prof').val();
        if (password.length == 0 || password.length <= 5) {
            $("#pwd_err").html('Please provide password at least 5 symbols');
            return false;
        }

        var address = $('#address_prof').val();
        if (address == '') {
            $("#addr_err").html('Please provide address');
            return false;
        }

        var prof_title = $("#title_prof").val();
        if (prof_title == '') {
            $("#title_err").html('Please provide title');
            return false;
        }

        var department = $("#department_prof").val();
        if (department == '') {
            $("#department_err").html('Please provide department');
            return false;
        }

        var school = $("#school_prof").val();
        if (school == '') {
            $("#school_err").html('It is required');
            return false;
        }

        var new_group_name = $('#new_group').val();
        if (new_group_name == '') {
            $("#new_group_err").html('Please provide course name');
            return false;
        }

        if (fn != '' && ln != '' && email != '' && validateEmail(email) == true && password != '' && address != '' && prof_title != '' && department != '' && school != '' && new_group_name != '') {
            console.log('Inside if when all data are provided ...');
            // Check is email already in use?
            $.post("lms/getGroups.php", {
                email: email
            }).done(function (data) {
                console.log('Response (email exists): ' + data);
                if (data > 0) {
                    $("#email_err").html('Provided email already in use');
                    return false;
                }
                else {

                    // Email is not used so we can proceed with signup
                    var courses = 3;

                    // Check is group name already exists?
                    $.post("lms/checkCourseName.php", {
                        new_group_name: new_group_name
                    }).done(function (data) {
                        console.log('Response (group exists): ' + data);

                        if (data == 0) {
                            // Place here AJAX request
                            var user_type = 'tutor';
                            var url = 'http://globalizationplus.com/lms/login/signup.php';
                            var query = {user_type: user_type,
                                firstname: fn,
                                lastname: ln,
                                username: email,
                                password: password,
                                email: email,
                                school: school,
                                title: prof_title,
                                department: department,
                                course: courses,
                                group: new_group_name,
                                address: address};
                            $('#signupwrap_professor').fadeTo("slow", 0.33);
                            $.post(url, query).done(function (data) {
                                $('#signupwrap_professor').fadeTo("slow", 1);
                                $('#signupwrap_professor').height(30);
                                $("#signupwrap_professor").html(data);
                            }).fail(function () {
                                $("#signupwrap_professor").html('Ops something goes wrong ...');
                            });
                            return false;
                        }// end if data==0 when group name is not exist
                        else {
                            $("#new_group_err").html('Course already exists');
                            return false;
                        } // end else
                        return false;
                    }); // end done function
                    return false;
                } // end else when email is not used
            }); // end of done(function (data)
        } // end if fn!='' && ln!='' && email!='' &&         
    }); // end of $('#signupform_prof').on('submit')    

    function validateEmail(email) {
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return re.test(email);
    }

    // Code related to groups typehead
    $.post("lms/update_groups.php", function () {
        console.log('Groups updated ... ');
        $.getJSON("lms/groups.json", function (data) {

            var substringMatcher = function (strs) {
                return function findMatches(q, cb) {
                    var matches, substringRegex;

                    // an array that will be populated with substring matches
                    matches = [];

                    // regex used to determine if a string contains the substring `q`
                    substrRegex = new RegExp(q, 'i');

                    // iterate through the pool of strings and for any string that
                    // contains the substring `q`, add it to the `matches` array
                    $.each(strs, function (i, str) {
                        if (substrRegex.test(str)) {
                            matches.push(str);
                        }
                    });

                    cb(matches);
                };
            };

            var groups = data;

            $('#the-basics .typeahead').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'groups',
                source: substringMatcher(groups)
            });
        }); // end of $.getJSON

    }); // end of $.post("lms/update_groups.php"

    /*******************************************************************************
     * 
     * Code related to signing process
     * 
     ******************************************************************************/
    $('#loginform').on('change', function () {
        $("#email_err").html('');
        $("#pwd_err").html('');
    });

    $('#loginform').on('submit', function () {

        var email = $('#username').val();
        var password = $('#password').val();


        if (email.length == 0) {
            $("#email_err").html('Please provide email');
            return false
        }

        if (password.length == 0) {
            $("#pwd_err").html('Please provide password');
            return false;
        }

        if (email.length > 0 && password.length > 0) {
            //HTMLFormElement.prototype.submit.call($('#loginform')[0]);
            return true;
        }

    }) // ('#loginform').on('submit', function ()    

    /*******************************************************************************
     * 
     *               Code related to enroll key form
     * 
     ******************************************************************************/

    $('#enrol_form').on('submit', function (event) {
        event.preventDefault();
        var userid = $('#userid').val();
        var key = $('#key').val();
        if (key == '') {
            $('#key_err').html('Please provide enroll key');
        }
        else {
            var query = {userid: userid,
                code: key};
            $.post('../../update_enroll_key.php', query).done(function (data) {
                if (data == 'ok') {
                    $('#key_err').html('<span style="green">Ok</span>');
                    location.reload();
                }
                else {
                    $('#key_err').html('Enroll key expired or invalid');
                }
            });
        } // end else
    });
}); // end of $(document).ready(function ()


