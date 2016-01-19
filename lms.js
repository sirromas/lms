/*******************************************************************************
 * 
 * Code related to signup process
 * 
 ******************************************************************************/

$(document).ready(function () {
    $('#a_student').on('click', function () {
        // console.log("Student signup clicked");
        getSignupForm('student');
    });

    $("#a_tutor").click(function () {
        // console.log("Tutor signup clicked");
        getSignupForm('tutor');
    });

    function getSignupForm(user) {

        $.post("lms/signup_form.php", {
            user: user
        }).done(function (data) {
            $("#signup_content").html(data);
        });
    }

    $('#courses').live('change', function () {
        var user_type = $("#user_type").val();
        //console.log('User type: ' + user_type);
        //if (user_type == 'student') {
        var course = $("#courses").val();
        //console.log('Course selected: ' + course);
        $.post("lms/getGroups.php", {
            course: course
        }).done(function (data) {
            $("#for_gr").html(data);
        });
        //}
    });

    function validateEmail(email) {
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return re.test(email);
    }

    function isEmailUsed(email) {
        $.post("getGroups.php", {
            email: email
        }).done(function (data) {
            return data;
        })

    }

    function isUserUsed(username) {
        $.post("lms/getGroups.php", {
            username: username
        }).done(function (data) {
            console.log('UserStatus: ' + data);
            return data;
        }).fail(function (data) {
            console.log('Error occured:' + data);
        })
    }


    function processEmail(email) {
        valid = validateEmail(email);
        used = isEmailUsed(email)
        if (valid == true && used == 0) {
            return true;
        }
        else {
            return false;
        }
    }

    $('#signupform').live('change', function () {
        $("#fn_err").html('');
        $("#ln_err").html('');
        $("#email_err").html('');
        $("#pwd_err").html('');
        $("#course_err").html('');
        $("#group_err").html('');
        $("#group_err").html('');
        $("#new_group_err").html('');
        $("#department_err").html('');
        $("#school_err").html('');
        $("#addr_err").html('');
        $("#title_err").html('');

        var create_groups = $('#new_gr').is(':checked');
        //console.log('Create groups: ' + create_groups);
        if (create_groups) {
            $('#new_groups').prop('disabled', false);
            var new_groups = $('#new_groups').val();
            //console.log('New groups: ' + new_groups);
        }
        else {
            $('#new_groups').prop('disabled', true);
        }
    });

    $('#signupform').live('submit', function () {

        var user_type = $('#user_type').val();

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
        $.post("lms/getGroups.php", {
            email: email
        }).done(function (data) {
            // console.log('Email status: ' + data);
            if (data != 0) {
                $("#email_err").html('Provided email already in use');
                return false;
            }
            if (validateEmail(email) != true) {
                $("#email_err").html('Provided email is incorrect');
                return false;
            }
        });

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

        var prof_title = $("#title").val();
        if (prof_title == '') {
            $("#title_err").html('Please provide title');
            return false;
        }

        var department = $("#department").val();
        if (department == '') {
            $("#department_err").html('Please provide department');
            return false;
        }

        var school = $("#school").val();
        console.log('School: ' + school);
        console.log('User type: ' + user_type);
        if (school == '') {
            if (user_type == 'student') {
                $("#school_err").html('Please provide school name');
                return false;
            }
            else {
                $("#school_err").html('Please provide online page');
                return false;
            }
        }


        /*
         var courses = $('#courses').val();        
         if ($('#courses').val() == 0) {
         $("#course_err").html('Please select course');
         return false;
         }
         */

        // For now we have only one course
        var courses = 3;

        var groups = $('#groups').val();
        if (groups == 0 && user_type == 'student') {
            $("#group_err").html('Please select group');
            return false;
        }

        var new_group_name = $('#new_group').val();
        if (new_group_name == '' && user_type == 'tutor') {
            $("#new_group_err").html('Please provide new group name');
            return false;
        }

        var url = 'http://globalizationplus.com/lms/login/signup.php';
        if (user_type == 'student') {
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
        }
        else {
            query = {user_type: user_type,
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
        }

        $.post("lms/getGroups.php", {
            email: email
        }).done(function (data) {
            console.log('Data: ' + data);
            console.log('Done Email status: ' + data);
            if (data != 0) {
                $("#email_err").html('Provided email already in use');
                return false;
            } // end if data != 0 
            else {
                $('.CSSTableGenerator').fadeTo("slow", 0.33);
                $('#spinner').show();
                $.post(url, query).done(function (data) {
                    console.log('Else data: ' + data);
                    $("#signup_content").html(data);
                }).fail(function (data) {
                    console.log('Fail data: ' + data);
                    $("#signup_content").html('Ops something wrong ...');
                    //$("#signup_content").html(data);
                    return false;
                })
                return false;
            } // end else
        }) // end .done(function (data)
        return false;
    }); // end of signup form submit event

})

/*******************************************************************************
 * 
 * Code related to signing process
 * 
 ******************************************************************************/
$(document).ready(function () {

    $("#user_type").on('change', function () {
        var user_type = $("#user_type").val();
        // console.log(user_type);
        if (user_type == 5) {
            $("#tr_code").show();
        } else {
            $("#tr_code").hide();
        }

    });

    $('#loginform').on('change', function () {
        $("#email_err").html('');
        $("#pwd_err").html('');
        $("#user_err").html('');
        $("#code_err").html('');
    });

    function checkTypeCode(email, user_type, code) {
        var query = {user_type: user_type,
            username: email,
            code: code};
        $.post('lms/login_verify.php', query).done(function (data) {
            var user_data = $.parseJSON(data);
            var status = [user_data.type, user_data.code];
            //console.log('checkTypeCode' + status);
            return status;
        }); // .post('lms/login_verify.php', query).done
    }

    function signinSubmit(user_data) {
        console.log('signinSubmit: ' + user_data);
        if (user_data[0] == 0) {
            $("#user_err").html('Invalid user type');
            return false;
        }
        if (user_data[1] == 0) {
            $("#code_err").html('Invalid enrollment code');
            return false;
        }
        if (user_data[0] == 1 && user_data[1] == 1) {
            return true;
        }
    }

    $('#loginform').on('submit', function () {
        var user_type = $("#user_type").val();
        var email = $('#username').val();
        var password = $('#password').val();
        var code = $('#code').val();

        if (email.length == 0) {
            $("#email_err").html('Please provide email');
            return false
        }

        if (password.length == 0) {
            $("#pwd_err").html('Please provide password');
            return false;
        }

        if (user_type == 0) {
            $("#user_err").html('Please select user type');
            return false;
        }

        if (user_type == 5) {
            if (code.length == 0) {
                $("#code_err").html('Please provide enroll key');
                return false;
            }
        }

        var query = {user_type: user_type,
            username: email,
            code: code};
        $.post('lms/login_verify.php', query).done(function (data) {
            console.log(data);
            var user_data = $.parseJSON(data);
            if (user_data.type == 0) {
                $("#user_err").html('Invalid user type');
                return false;
            }
            if (user_data.code == 0) {
                $("#code_err").html('Invalid enrollment code');
                return false;
            }
            if (user_data.type == 1 && user_data.code == 1) {
                //("#loginform").submit();
                HTMLFormElement.prototype.submit.call($('#loginform')[0]);
            }
        }); // .post('lms/login_verify.php', query).done
        return false;
    }) // ('#loginform').on('submit', function ()    
}); // document).ready(function () 
