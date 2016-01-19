
$(document).ready(function () {

    $('#loginform').on('change', function () {
        $("#email_err").html('');
        $("#code_err").html('');
        $("#group_err").html('');
        $("#page_err").html('');
    });

    $('#loginform').on('submit', function () {

        var email = $('#email').val();
        var code = $('#code').val();
        var group = $('#groups').val();
        var page=$('#page').val();
        
        console.log('Group: '+group);

        if (email == '') {
            $("#email_err").html('Please provide email');
            return false
        }

        if (code == '') {
            $("#code_err").html('Please provide code');
            return false;
        }
        
        if (page =='') {
            $("#page_err").html('Please provide online page');
            return false;
        }

        if (group == 0) {
            $("#group_err").html('Please select group');
            return false;
        }      
        
        if (email != '' && code != '' && group != 0 && page!='') {
            $.post("confirm.php", {
                email: email,
                code: code,
                group: group,
                page:page
            }).done(function (data) {
                $("#confirm_status").html(data);
            }) // end .done(function()    
        } // end if email != '' && code != '' && group != 0

        return false;
    });

}); // document).ready(function () 