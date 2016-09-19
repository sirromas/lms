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
        var email = $('#email').val();
        var url = "http://globalizationplus.com/survey/";
        $.post(url, {email: email, result: result}).done(function (data) {
            alert('Thank you!');
        });
    }
    
    /*
    $(document).on('click', function (event) {
        if (event.target.id == '20' || event.target.id == '50' || event.target.id == '80' || event.target.id == '100') {
            sendReport(event.target.id);
        }
    });
    */

});

