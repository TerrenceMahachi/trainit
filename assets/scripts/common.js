console.log('pgs: ');

// CSRF (double-submit cookie): attach the csrf_token cookie to EVERY jQuery
// AJAX request as the X-CSRF-Token header. The server (postAuthMiddleware)
// rejects state-changing requests that don't echo the cookie back.
if (window.jQuery) {
    $(document).ajaxSend(function (event, xhr) {
        var m = document.cookie.match(/(?:^|;\s*)csrf_token=([a-f0-9]+)/);
        if (m) { xhr.setRequestHeader('X-CSRF-Token', m[1]); }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Set initial page_size from the cookie, if exists
    let pageSizeFromCookie = getCookie('page_size');
    if (pageSizeFromCookie) {
        const pageSizeControl = document.querySelector(`input[name="page_size"][value="${pageSizeFromCookie}"]`);

        // Check if the control exists before attempting to set its checked property
        if (pageSizeControl) {
            pageSizeControl.checked = true;
        }
    }

    // Attach event listeners to radio buttons
    document.querySelectorAll('input[name="page_size"]').forEach(function (radioButton) {
        radioButton.addEventListener('change', function () {
            let selectedPageSize = this.value;

            console.log('pgs: ' + selectedPageSize);

            // Save the selected page_size in a cookie
            setCookie('page_size', selectedPageSize, 31); // Save for 31 days

            // Reload the page with the selected page_size as a query parameter
            let url = new URL(window.location.href);
            // url.searchParams.set('page_size', selectedPageSize);
            window.location.href = url.href;
        });
    });
});

// Function to get a cookie by name
function getCookie(name) {
    let cookieArr = document.cookie.split(";");
    for (let i = 0; i < cookieArr.length; i++) {
        let cookiePair = cookieArr[i].split("=");
        if (name === cookiePair[0].trim()) {
            return decodeURIComponent(cookiePair[1]);
        }
    }
    return null;
}
function expireCookie(name) {
    const cookieValue = getCookie(name);
    if (cookieValue) {
        const expiryDate = new Date();
        expiryDate.setTime(expiryDate.getTime() + (-1 * 60 * 1000));
        document.cookie = `${name}=${cookieValue}; expires=${expiryDate.toUTCString()}; path=/`;
    }
}
// Function to set a cookie
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function CookieExists(cookieName) {
    // Get all cookies as a single string
    const allCookies = document.cookie;

    // Use a regular expression to check if the cookie name exists
    const cookieRegex = new RegExp('(^|; )' + encodeURIComponent(cookieName) + '=([^;]*)');

    // Test the cookie string against the regex
    return cookieRegex.test(allCookies);
}

function create_message(state, message) {
    return `<div class="alert alert-${state} alert-dismissible fade show" role="alert">
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>`
}


// Delegated from document — common.js loads in <head>, before the form
// inputs these target exist in the DOM, so a direct $(".show-p").on(...)
// binds to nothing. Delegation matches the selector at click time instead.
$(document).on("click", ".show-p", function () {
    var th = $(this)
    var x = document.getElementById(th.attr('data-target'));
    if (!x) return;
    if (x.type === "password") {
        x.type = "text"; th.removeClass('fa-eye'); th.addClass('fa-eye-slash');
    } else {
        x.type = "password"; th.removeClass('fa-eye-slash'); th.addClass('fa-eye');
    }
});

$(document).on("click", ".password-show", function () {
    var th = $(this)
    var x = document.getElementById(th.attr('data-target'));
    if (!x) return;
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
});
function show_confirm(message, callback, yesButtonText = 'Yes') {
    // Set modal content
    $('#confirm-message').text(message);
    console.log('syl')
    // Set text for the "Yes" button
    $('#confirm-action-btn-yes').text(yesButtonText);

    // Set callback for the "Yes" button click
    $('#confirm-action-btn-yes').off('click').on('click', function () {
        // Close the modal
        $('#confirmModal').modal('hide');

        // Execute the callback function
        if (typeof callback === 'function') {
            callback();
        }
    });

    // Show the modal
    $('#confirmModal').modal('show');
}
$(document).ready(function () {
    clearInterval()
    const checkInterval = 60000; // 1 minute in milliseconds
    const cookieName = 'user'; // Replace with the actual name of your cookie
    const loginUrl = site + '/home'; // Replace with the actual login page URL

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function extendCookieExpiry(name, minutes) {
        const cookieValue = getCookie(name);
        if (cookieValue) {
            const expiryDate = new Date();
            expiryDate.setTime(expiryDate.getTime() + (minutes * 60 * 1000));
            document.cookie = `${name}=${cookieValue}; expires=${expiryDate.toUTCString()}; path=/`;
        }
    }

    function checkCookie() {
        if (!getCookie(cookieName)) {
            //   window.location.href = loginUrl;
        }
    }

    // Check cookie expiration every minute
    setInterval(checkCookie, checkInterval);

    // Extend cookie expiry on user interaction
    $(document).on('mousemove click keypress', function () {
        extendCookieExpiry(cookieName, 30); // Extend expiry by 30 minutes on interaction
    });
});

function showToast(message) {
    // Create a new toast element

    var toast = $('<div class="toast"></div>').html(message);
    // $('#toast-container').html(message)
    // console.log('tc: ' +  $('#toast-container').html())

    // Add the toast to the container
    $('#toast-container').append(toast);
    //console.log('tc: ' +  $('#toast-container').html())
    // Show the toast
    setTimeout(function () {
        toast.addClass('show');
    }, 10);

    // Hide the toast after 3 seconds
    setTimeout(function () {
        toast.removeClass('show');
        setTimeout(function () {
            toast.remove();
        }, 300); // Wait for the transition to end before removing the element
    }, 3000);
}
async function request(endpoint, data = {}) {

    data['session'] = localStorage.getItem('session'); // Include the user ID in the request data

    //console.log('Request data: ' + JSON.stringify(data));

    try {
        let response;
        response = await $.ajax({
            url: endpoint,
            type: 'POST',
            data: data,
            dataType: 'json',
            error: function (xhr, status, error) {
                alert('Error fetching data:', xhr.responseText);
            }
        });
        //   console.log('Request data: ' + JSON.stringify(response));
        if (response.responseCode == "001") {
            if (response.status == "error") {
                alert(response.msg)
                document.location.href = "account-login"

            }
        }
        // if(response.toString().indexOf('Session')!=-1){alert('session expired'); document.location.href="account-login"}

        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// --- Idle soft-lock handling -------------------------------------------------
// When the server reports the session was paused after inactivity, any AJAX
// call returns { locked: true, resume/redirect: "<url>" }. Send the user to the
// resume challenge so they can re-authenticate without a full logout.
function handleSoftLock(text) {
    try {
        var data = JSON.parse(text);
        var target = data && data.locked && (data.resume || data.redirect);
        if (target) {
            window.location.href = target;
            return true;
        }
    } catch (e) { /* non-JSON response, ignore */ }
    return false;
}

// jQuery AJAX ($.ajax / $.post used across the resource scripts).
if (window.jQuery) {
    jQuery(document).ajaxComplete(function (event, xhr) {
        handleSoftLock(xhr.responseText);
    });
}

// Native fetch() (used directly in several views). Clone the response so the
// caller still gets an untouched body; only inspect JSON payloads.
if (window.fetch && !window.__softLockFetchWrapped) {
    window.__softLockFetchWrapped = true;
    var _nativeFetch = window.fetch.bind(window);
    window.fetch = function () {
        return _nativeFetch.apply(this, arguments).then(function (response) {
            var type = response.headers && response.headers.get && response.headers.get('content-type');
            if (type && type.indexOf('application/json') !== -1) {
                response.clone().text().then(handleSoftLock).catch(function () {});
            }
            return response;
        });
    };
}
