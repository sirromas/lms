
/*******************************************************************************
 * 
 * Code related to payment
 * 
 ******************************************************************************/

function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}

$(document).ready(function () {

    $('.dsR1652').on('submit', function () {

        var name = $("[name='cds_name']").val();
        var address = $("[name='cds_address_1']").val();
        var city = $("[name='cds_city']").val();
        var state = $("[name='cds_state']").val();
        var zip = $("[name='cds_zip']").val();
        var email = $("[name='cds_email']").val();
        var pay_type = $("[name='cds_pay_type']").val();
        var cc_number = $("[name='cds_cc_number']").val();
        var exp_month = $("[name='cds_cc_exp_month']").val();
        var exp_year = $("[name='cds_cc_exp_year']").val();

        if (name !== '' && address !== ''
                && city !== '' && state !== '' && state !== 'State'
                && zip !=='' && email !== ''
                && pay_type !== '' && cc_number !== ''
                && exp_month !== '' && exp_year !== '') {
            if (validateEmail(email) !== true) {
                $(".errormsg").html('Provided email is incorrect');
                return false;
            }
            else {
                var url = 'https://globalizationplus.com/lms/payments/process.php';
                query = {cds_name: name,
                    cds_address_1: address,
                    cds_city: city,
                    cds_state: state,
                    cds_zip: zip,
                    cds_email: email,
                    cds_pay_type: pay_type,
                    cds_cc_number: cc_number,
                    cds_cc_exp_month: exp_month,
                    cds_cc_exp_year: exp_year};

                $('#payment_form').fadeTo("slow", 0.33);
                $.post(url, query).done(function (data) {
                    $('#payment_form').fadeTo("slow", 1);
                    $("#payment_form").html(data);
                });
                return false;
            } // end else
        } // end if name != '' && address != ''
        else {
            $(".errormsg").html('Please provide all fields');
            return false;
        }
    });
});


