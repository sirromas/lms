

$(document).ready(function () {

    $("#sa").click(function () {
        $.post("getGroups.php", {
            email: 'sirromas@ukr.net'
        }).done(function (data) {
            console.log('Email status: ' + data);
            if (data != 0) {
                alert('Alert email already in use');
                return false;
            } // end if data != 0 
            else {
                alert('Maing rquest to server...');
            } // end else
        }) // end .done(function (data)
    }); // end of $("#sa").click(function ()
}); // document).ready(function () 
