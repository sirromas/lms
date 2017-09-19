$(document).ready(function () {
    console.log("ready!");

    $("#grades").click(function () {
        console.log('Clicked ...');
        if ($('#student_grades').is(":visible")) {
            $('#student_grades').hide();
        } // end if
        else {
            $('#student_grades').show();
        } // end else

    });

});