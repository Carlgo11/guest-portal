$(document).ready(function () {
    const usp = new URLSearchParams(window.location.search);
    if (usp.get('ssid') != null) {
        let ssid = decodeURIComponent(usp.get('ssid').replace(/\+/g, ' '));
        $('.title').text(ssid + " Wi-Fi");
        document.title = decodeURIComponent(ssid);
    }
})

$('#manual').click(function () {
    displayView('#approval');
    const usp = new URLSearchParams(window.location.search);
    setInterval(function () {
        if (navigator.onLine) {
            displayView('#success');
            setTimeout(function () {
                window.location.replace(decodeURIComponent(usp.get('url')));
            }, 2000);
        }
    }, 5000);
})

$('#otp').keyup(function () {
    let foo = $(this).val().split("-").join(""); // remove hyphens
    if (foo.length > 4) {
        foo = foo.match(new RegExp('.{1,5}', 'g')).join("-");
    }
    $(this).val(foo);
    if ($(this).val().length === 11) {
        $('form').submit();
    }
})

$('form').submit(function (e) {
    // Prevent browser from connecting itself to action path
    e.preventDefault();

    const usp = new URLSearchParams(window.location.search);

    const data = {
        code: $('#otp').val(),
        ap: usp.get('ap'),
        mac: usp.get('id'),
        t: usp.get('t')
    };

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        dataType: 'json',
        data: JSON.stringify(data),
        contentType: 'application/json',
        timeout: 5000,
        accepts: {
            text: 'application/json'
        },
        success: function () {
            displayView('#success');
            setTimeout(function () {
                window.location.replace(decodeURIComponent(usp.get('url')));
            }, 2000);
        },
        beforeSend: function () {
            // Display #loading
            displayView('#loading')
        },
        error: function (res) {
            displayView('#error');
            console.error(res);
            $('#error-msg').text('Error: ' + res.responseJSON['error']);
        }
    });
});