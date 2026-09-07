
$(document).on('click', '#btn_edit_item', function () {
    $('#loadingOverlay').show();
    console.log('submitting...')
    var msg = $('#form_result'), _btn = $(this), _form = $('#update_item_form').serialize();
    msg.html(""); _btn.prop("disabled", true); _btn.html("Processing...");

    $.ajax({
        url: site + "/update-" + table, type: 'POST', dataType: 'json', data: _form,
        complete: function (data) {
            $('#loadingOverlay').hide();

            _btn.prop("disabled", false); console.log("rs: " + data.responseText.toString());
            var result = typeof data.responseJSON === 'object' ? data.responseJSON : JSON.parse(data.responseText.toString());

            _btn.html("Update again");

            //alert(rString) //get_pagination();
            if (result.status == 1) {

                msg.html(create_message("success", result.message));

                //	document.location.reload();
            } else { _btn.html("Try again"); msg.html(create_message("danger", result.message)); }
        }
    });

})

$(document).on('click', '#btn_create_item', function () {
    $('#loadingOverlay').show();
    console.log('submitting...')
    var msg = $('#form_result'), _btn = $(this), _form = $('#create_item_form').serialize();
    msg.html(""); _btn.prop("disabled", true); _btn.html("Processing...");

    $.ajax({
        url: site + "/create-" + table, type: 'POST', dataType: 'json', data: _form,
        complete: function (data) {
            $('#loadingOverlay').hide();

            _btn.prop("disabled", false); console.log("rs: " + data.responseText.toString());
            var result = typeof data.responseJSON === 'object' ? data.responseJSON : JSON.parse(data.responseText.toString());

            _btn.html("Add another");

            //alert(rString) //get_pagination();
            if (result.status == 1) {

                msg.html(create_message("success", result.message));

                //	document.location.reload();
            } else { _btn.html("Try again"); msg.html(create_message("danger", result.message)); }
        }
    });

})
