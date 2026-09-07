
$('#btn_create_user').on('click', function () {
    $('#loadingOverlay').show();
    console.log('submitting...')
    var msg = $('#form_result'), _btn = $(this), _form = $('#create_user_form').serialize();
    msg.html(""); _btn.attr("disabled", "true"); _btn.html("Processing...");

    $.ajax({
        url: site + '/create-user', type: 'POST', dataType: 'application/json', data: _form,
        complete: function (data) {
            $('#loadingOverlay').hide();

            _btn.removeAttr("disabled"); console.log("rs: " + data.responseText.toString());
            var result = JSON.parse(data.responseText.toString());

            _btn.html("Add another");

            //alert(rString) //get_pagination();
            if (result.status == 1) {

                msg.html(create_message("success", result.message));

                //	document.location.reload();
            } else { _btn.html("Try again"); msg.html(create_message("danger", result.message)); }
        }
    });

})