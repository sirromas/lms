
$(document).ready(function () {

    $('#loginform').on('change', function () {
        $("#email_err").html('');
        $("#pwd_err").html('');
    });

    $('#loginform').on('submit', function () {

        var email = $('#username').val();
        var password = $('#password').val();

        if (email == '') {
            $("#email_err").html('Please provide username');
            return false
        }

        if (password == '') {
            $("#pwd_err").html('Please provide password');
            return false;
        }

        if (email != '' && password != '') {
            return true;
        }
    });

    $("#group_codes").click(function () {
        $("#group_codes_content").show();
        $("#promo_codes_content").hide();
    });

    $("#promo_codes").click(function () {
        $("#group_codes_content").hide();
        $("#promo_codes_content").show();
    });

    $("#new_promo").click(function () {
        if (confirm("Create new promo codes?")) {
            $('.CSSTableGenerator').fadeTo("slow", 0.33);
            $.post("../payments/promo.php", {
                email: 'ss'
            }).done(function () {
                $.post("./refresh_promo_codes.php", {
                    email: 'dd'
                }).done(function (data) {
                    $('.CSSTableGenerator').fadeTo("slow", 1);
                    $("#promo_codes_content").html(data);
                    $("#promo_codes_content").show();
                }) // end .done(function (data)
            }) // end .done(function (data)
        } // end "#new_promo").click(function ()
    });

    $("#logout").click(function () {
        if (confirm("Logout from the system?")) {
            $.post("logout.php", {
                email: 'ss'
            }).done(function () {
                window.location.assign(document.URL);
            }) // end .done(function (data)    
        } // end if confirm
    }); // "#logout").click(function ()

    $("#logout2").click(function () {
        if (confirm("Logout from the system?")) {
            $.post("logout.php", {
                email: 'ss'
            }).done(function () {
                window.location.assign(document.URL);
            }) // end .done(function (data)    
        } // end if confirm
    }); // "#logout").click(function ()
}); // document).ready(function () 