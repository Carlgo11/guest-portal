$(document).ready(function () {
    if (getUrlVars()['ssid'] != null) {
        let ssid = decodeURIComponent(getUrlVars()['ssid'].replace(/\+/g, ' '));
        $('#topic').text(ssid + " WiFi");
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
    $.ajax({
        type: 'GET',
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
        },
        beforeSend: function () {
            // Display #loading
            displayView('#loading')
        },
        error: function (res) {
            // Display #error with #error-msg set
            console.error(res);
            displayView('#error');
            $('#error-msg').text('Error: ' + res.statusText);

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