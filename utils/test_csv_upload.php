
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<p>File:  <input type='file' id='price_scv'></p>
<p><span id="price_err"></span></p> &nbsp;&nbsp; <button type='button' class='btn btn-default' id='upload_price_file'>Ok</button>


<script type="text/javascript">

    $(document).ready(function () {

        $('body').on('click', function (event) {

            if (event.target.id == 'upload_price_file') {
                var file = $('#price_scv').val();
                if (file == '') {
                    $('#price_err').html('Please select CSV file to upload');
                } // end if
                else {
                    $('#price_err').html('');
                    var file_data = $('#price_scv').prop('files');
                    var url = '/lms/utils/upload_test_csv.php';
                    var form_data = new FormData();
                    $.each(file_data, function (key, value) {
                        form_data.append(key, value);
                    });
                    $('#loader').show();
                    $.ajax({
                        url: url,
                        data: form_data,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        success: function (data) {
                            console.log(data);
                            $("[data-dismiss=modal]").trigger({type: "click"});
                            $('#myModal').data('modal', null);
                            //document.location.reload();
                        } // end of success
                    }); // end of $.ajax ..
                } // end else
            }


        }); // end of body click


    });

</script>
