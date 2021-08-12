$('form').submit(function (e) {
    // Prevent browser from connecting itself to action path
    e.preventDefault();

    const data = {
        uses: $('#uses').val(),
        expiry: $('#expiry').val(),
        duration: $('#duration').val()
    };

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        dataType: 'json',
        data: JSON.stringify(data),
        contentType: 'application/json',
        timeout: 5000,
        success: function (data) {
            displayView('#voucher-view');
            $('#v-id').text(data['voucher'].replace(/(.{5})/g, "$1-").slice(0, -1))
            if ($('#duration').val()) {
                if ($('#duration').val() > 1) {
                    let hours = 'hrs'
                } else {
                    let hours = 'hr'
                }
                $('#v-duration').show().append($('#duration').val() + hours)
            }
            if ($('#speed').val()) {
                $('#v-speed').show().append($('#speed').val() + 'MiB/s')
            }
            if ($('#expiry').val()) {
                $('#v-expire').show().append($('#expiry').val())
            }
        },
        error: function (res) {
            console.error(res);
            displayView('#error');
            $('#error-msg').text('Error: ' + res.responseJSON['error']);
        }
    });
});

$('#v-id').click(function (e) {
    e.preventDefault();
    navigator.share({
        title: 'Voucher',
        text: $(this).text()
    }).then(r => console.error(r)).catch(err => {
        console.log(`Couldn't share because of`, err.message);
    });
})
