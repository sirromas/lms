
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
        console.log("Form submitted");

        /*        
         .cds_name
         .cds_address_1
         .cds_city
         .cds_state
         .cds_zip
         .cds_email
         .cds_pay_type
         .cds_cc_number
         .cds_cc_exp_month
         .cds_cc_exp_year        
         */
        var name = $("[name='cds_name']").val();
        console.log('Name: ' + name);

        var address = $("[name='cds_address_1']").val();
        console.log('Address: ' + address);

        var city = $("[name='cds_city']").val();
        console.log('City: ' + city);

        var state = $("[name='cds_state']").val();
        console.log('State: ' + state);

        var zip = $("[name='cds_zip']").val();
        console.log('Zip: ' + zip);

        var email = $("[name='cds_email']").val();
        console.log('Email: ' + email);

        var pay_type = $("[name='cds_pay_type']").val();
        console.log("Card type: " + pay_type);

        var cc_number = $("[name='cds_cc_number']").val();
        console.log("Card number: " + cc_number);

        var exp_month = $("[name='cds_cc_exp_month']").val();
        console.log("Expiration month: " + exp_month);

        var exp_year = $("[name='cds_cc_exp_year']").val();
        console.log("Expiration year: " + exp_year);

        if (name != '' && address != ''
                && city != '' && state != '' && state!='State'
                && zip != '' && email != ''
                && pay_type != '' && cc_number != ''
                && exp_month != '' && exp_year != '') {
            if (validateEmail(email) != true) {
                $(".errormsg").html('Provided email is incorrect');
                return false;
            }
            else {
                return true;
            }
        }
        else {
            $(".errormsg").html('Please provide all fields');
            return false;
        }




    });

})


