
var account_url = site + "/validate-login";
console.log('rs: ' + account_url)
function login() {
    //alert('data.toString()')
    var msg = $('#msg'), _btn = $("#submit_btn"), _form = $('#_form').serialize();
    msg.html(""); _btn.attr("disabled", "true"); _btn.html("Processing...");

    $.ajax({
        url: account_url, type: 'post', dataType: 'application/json', data: _form,
        complete: function (data) {
            _btn.removeAttr("disabled"); console.log("rs: " + data.responseText.toString());
            var result = JSON.parse(data.responseText.toString());

            _btn.html("Login");

            //alert(rString) //get_pagination();
            if (result.status == 1) {
                localStorage.setItem('session', result.session);

                msg.html(create_message("success", "login sucessful: "));
                if (!localStorage.getItem("returnTo")) {
                    document.location.href = site + "/dashboard";
                } else {
                    var loc = localStorage.getItem("returnTo");
                    localStorage.removeItem("returnTo");
                    if (loc.indexOf('home') != -1) {
                         document.location.href = site + "/dashboard";
                    } else {
                        document.location.href = loc; 
                    }
                }
                //	document.location.reload();
            } else { _btn.html("Try again"); msg.html(create_message("danger", result.msg)); }
        }
    });

};

