$('#login').submit(function (e) {
    e.preventDefault();
    const usp = new URLSearchParams(window.location.search);

    const data = {
        username: $('#username').val(),
        password: $('#password').val(),
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
            window.location.replace(decodeURIComponent(usp.get('url')));
        },
        error: function (res) {
            displayView('#error');
            console.error(res);
            $('#error-msg').text('Error: ' + res.responseJSON['error']);
        }
    });
})