
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
        var page = $('#page').val();

        console.log('Group: ' + group);

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

        if (email != '' && code != '' && page != '') {
            $("#group_err").html('');
            $.post("confirm.php", {
                email: email,
                code: code,
                group: 'aa',
                page: page
            }).done(function (data) {
                $("#confirm_status").html(data);
            }) // end .done(function()    
        } // end if email != '' && code != '' && group != 0
        return false;
    }); // end of $('#loginform').on('submit', function ()

    // Code related to groups typehead
    $.getJSON("../groups.json", function (data) {

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


}); // document).ready(function () 