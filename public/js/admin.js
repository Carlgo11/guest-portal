$('form').submit(function (e) {
    // Prevent browser from connecting itself to action path
    e.preventDefault();

    const data = {
        uses: $('#uses').val(),
        expiry: new Date($('#validity').val()).getTime() / 1000,
        duration: new Date($('#duration').val()).getTime() / 1000

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
                $('#v-duration').show().append($('#duration').val())
            }
            if ($('#speed').val()) {
                $('#v-speed').show().append($('#speed').val() + 'MiB/s')
            }
            if ($('#validity').val()) {
                $('#v-expire').show().append($('#validity').val())
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

$(function () {
    const duration = new Date()
    duration.setHours(duration.getHours() + 24);
    $('#duration').val(duration.toLocaleString("sv-SE").slice(0, -3))

    const validity = new Date()
    validity.setHours(validity.getHours() + 12);
    $('#validity').val(validity.toLocaleString("sv-SE").slice(0, -3))
});
