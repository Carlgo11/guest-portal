$(document).ready(function () {
    if (getUrlVars()['ssid'] != null) {
        let ssid = decodeURIComponent(getUrlVars()['ssid'].replace(/\+/g, ' '));
        $('.title').text(ssid + " WiFi");
        document.title = decodeURIComponent(ssid);
    }
});

function getUrlVars() {
    const vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
        function (m, key, value) {
            vars[key] = value;
        });
    return vars;
}

$('form').submit(function (e) {
    // Prevent browser from connecting itself to action path
    e.preventDefault();

    let data = getUrlVars();
    let form = $(this);
    data['code'] = form.serializeArray()[0]['value'];
    $.ajax({
        type: 'POST',
        url: form.attr('action'),
        dataType: 'json',
        data: data,
        timeout: 5000,
        accepts: {
            text: 'application/json'
        },
        success: function () {
            // Display success window?
            displayView('#success');
            setTimeout(function () {
                window.location.replace(decodeURIComponent(getUrlVars()['url']));
            }, 2000);
        },
        beforeSend: function () {
            // Display #loading
            displayView('#loading')
        },
        error: function (res) {
            // Display #error with #error-msg set
            displayView('#error');
            console.error(res);
            $('#error-msg').text('Error: ' + res.responseJSON['error']);

        }

    });


});

$('#manual').click(function (e) {
    displayView('#approval');
});

function displayView(view) {
    $('.view').hide();
    $(view).show();
}
$('.btn-back').click(function () {
    location.reload();
});


$('#otp').keyup(function () {

    var foo = $(this).val().split("-").join(""); // remove hyphens
    if (foo.length > 0) {
        foo = foo.match(new RegExp('.{1,5}', 'g')).join("-");
    }
    $(this).val(foo);
    if ($(this).val().length == 11) {
        $('form').submit();
    }
});