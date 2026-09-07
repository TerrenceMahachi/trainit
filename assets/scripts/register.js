
var account_url = site + "/register";
console.log('rs: ' + account_url)

// The verification question is generated and rendered SERVER-SIDE, and the
// answer is validated on the server (see app/Helpers/Captcha.php). The client
// only does light UX checks and submits the typed answer.

function submit() {

    var lbtn = $('#submit_btn'), msg = $('#msg'), error = false, erromsg = "";

    if ($("#txt_not_robot_answer").val() == "") {
        erromsg = "Please answer the verification question "; error = true;
    }

    if ($('[name=name]').val().indexOf('http') != -1) { erromsg += "<br>Invalid name, http is a bad word to include in your name "; error = true; }
    if (!error) {
        register();
    } else {
        msg.html(create_message("danger", erromsg)); lbtn.html("Try again");
    }

};

function register(source = 'internal') {
    var msg = $('#msg'), _btn = $("#submit_btn"), _form = $('#_form').serialize();
    msg.html(""); _btn.attr("disabled", "true"); _btn.html("Processing...");

    $.ajax({
        url: account_url, type: 'post', dataType: 'json', data: _form,
        success: function (response) {
            _btn.removeAttr("disabled");
            _btn.html('');
            if (response.status == 1) {
                localStorage.setItem('session', response.session);
                show_confirm('Thank you for registering, your account has been created and you will now be redirected to your dashboard. Please remember to check your mailbox for your email confirmation message?', function () {
                    document.location.href = site + "/dashboard"; console.log('sy')
                });

            } else {
                _btn.removeAttr("disabled"); _btn.html("Try again"); msg.html(create_message("danger", response.msg));
            }
        },
        error: function (xhr) {
            var fallback = "Server error. Please try again or contact support if it continues.";
            _btn.removeAttr("disabled");
            _btn.html("Try again");
            msg.html(create_message("danger", xhr.responseText || fallback));
        }
    });

};
