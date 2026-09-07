

$('#btn_stop_login').on('click', function () {
    $('#loadingOverlay').show();
    console.log('submitting...')
    var msg = $('#update_user_form_result'), _btn = $(this);
    msg.html(""); _btn.attr("disabled", "true"); _btn.html("Processing...");
    response =  $.ajax({
        url: site + '/block-user-login', // Replace with your server endpoint
        method: 'POST',
        data: { 'user': $('#user').val() },

        success: function (response) {
            console.log('rsp: ' + response)
             alert('Account blocked successfully');
             document.location.reload();
            // Handle success (e.g., redirect or show a success message)
        },
        error: function (err) {
            console.log('rsp: ' + err)
            _btn.html("Try again"); msg.html(create_message("danger", result.message)); 

            // Handle error (e.g., show error message)
        }
    }).always(function () {
        _btn.removeAttr("disabled");
        $('#loadingOverlay').hide();
        // This will run regardless of success or error
    });;
    

})


$('#btn_allow_login').on('click', function () {
    $('#loadingOverlay').show();
    console.log('submitting...')
    var msg = $('#update_user_form_result'), _btn = $(this);
    msg.html(""); _btn.attr("disabled", "true"); _btn.html("Processing...");
    response =  $.ajax({
        url: site + '/unblock-user-login', // Replace with your server endpoint
        method: 'POST',
        data: { 'user': $('#user').val() },

        success: function (response) {
            console.log('rsp: ' + response)
             alert('Account activated successfully');
             document.location.reload();
            // Handle success (e.g., redirect or show a success message)
        },
        error: function (err) {
            console.log('rsp: ' + err)
            _btn.html("Try again"); msg.html(create_message("danger", result.message)); 

            // Handle error (e.g., show error message)
        }
    }).always(function () {
        _btn.removeAttr("disabled");
        $('#loadingOverlay').hide();
        // This will run regardless of success or error
    });;
    

})

$('#btn_update_password').on('click', function () {
    $('#loadingOverlay').show();
    console.log('submitting...')
    var msg = $('#update_user_form_result'), _btn = $(this);
    msg.html(""); _btn.attr("disabled", "true"); _btn.html("Processing...");
    response =  $.ajax({
        url: site + '/update-password', // Replace with your server endpoint
        method: 'POST',
        data: { 'user': $('#user').val(), 'password': $('#password').val() },

        success: function (response) {
            console.log('rsp: ' + response)
            var result = JSON.parse(response);
            msg.html(create_message("success", result.message)); 
            _btn.html("Update Again");
            // Handle success (e.g., redirect or show a success message)
        },
        error: function (err) {
            console.log('rsp: ' + err)
            _btn.html("Try Again"); msg.html(create_message("danger", err)); 

            // Handle error (e.g., show error message)
        }
    }).always(function () {
        _btn.removeAttr("disabled");
        $('#loadingOverlay').hide();
        // This will run regardless of success or error
    });;
    

})



