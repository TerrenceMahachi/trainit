$('#btn_submit_profile').on('click', function () {
    console.log('submitting...')
    $('#loadingOverlay').show();
    var msg = $('#update_profile_form_result'), _btn = $(this), _form = $('#update_profile_form').serialize();
    msg.html(""); _btn.attr("disabled", "true"); _btn.html("Processing...");

    $.ajax({
        url: site + "/update-profile", type: 'POST', dataType: 'application/json', data: _form,
        complete: function (data) {
            $('#loadingOverlay').hide();

            _btn.removeAttr("disabled"); console.log("rs: " + data.responseText.toString());
            var result = JSON.parse(data.responseText.toString());

            _btn.html("Update again");

            //alert(rString) //get_pagination();
            if (result.status == 1) {

                msg.html(create_message("success", result.message));

                //	document.location.reload();
            } else { _btn.html("Try again"); msg.html(create_message("danger", result.message)); }
        }
    });

})