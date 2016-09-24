
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
}); // end of document ready


