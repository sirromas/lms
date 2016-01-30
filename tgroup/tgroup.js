$(document).ready(function () {

    $('#groupform').on('change', function () {
        $("#email_err").html('');
        $("#code_err").html('');
        $("#page_err").html('');
        $("#group1_err").html('');
        $("#group2_err").html('');
        $("#group3_err").html('');
        $("#group4_err").html('');
    });

    $('#groupform').on('submit', function () {

        var email = $('#email').val();
        var code = $('#code').val();
        var page = $('#page').val();

        var group1 = $('#group1').val();
        var group2 = $('#group2').val();
        var group3 = $('#group3').val();
        var group4 = $('#group4').val();

        if (email == '') {
            $("#email_err").html('Please provide email');
            return false
        }

        if (code == '') {
            $("#code_err").html('Please provide code');
            return false;
        }

        if (page == '') {
            $("#page_err").html('Please provide online page');
            return false;
        }

        if (group1 == '') {
            $("#group1_err").html('Please provide course name');
            return false;
        }

        if (email != '' && code != '' && group1 != '' && page != '') {
            $.post("create_group.php", {
                email: email,
                code: code,
                page: page,
                group1: group1,
                group2: group2,
                group3: group3,
                group4: group4
            }).done(function (data) {
                console.log('Server response: '+data);
                $("#group_created").html("<span align='center'>"+data+"</span>");
            }); // end .done(function()    
        } // end if email != '' && code != '' && group != 0
        return false;
    });

}); // document).ready(function () 

