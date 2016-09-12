/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {

    function isValidEmailAddress(emailAddress) {
        var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
        return pattern.test(emailAddress);
    }


    function sendReport(result) {
        var title = $('#title').val();
        var email = $('#email').val();

        if (title == '' || email == '') {
            $('#survey_err').html('Please provide your title and email');
        } // end if title=='' || email==''
        else {
            if (!isValidEmailAddress(email)) {
                $('#survey_err').html('Please provide correct email address');
            } // end if !isValidEmailAddress(email)
            else {
                $('#survey_err').html('');
                // Make ajax request
                var url = "/survey/send_survey_results.php";
                $.post(url, {title: title, email: email, result: result}).done(function (data) {
                    $('#survey_err').html(data);
                });
            } // end else 
        } // end else 
    }


    $(document).on('click', function (event) {
        //console.log('Event ID: ' + event.target.id);

        if (event.target.id == '20' || event.target.id == '50' || event.target.id == '80' || event.target.id == '100') {
            sendReport(event.target.id);
        }

    });

});

