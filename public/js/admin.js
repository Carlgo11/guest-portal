$('form').submit(function (e) {
    // Prevent browser from connecting itself to action path
    e.preventDefault();

    const data = {
        uses: $('#uses').val(),
        expiry: new Date($('#validity').val()).getTime() / 1000,
        duration: new Date($('#duration').val()).getTime() / 1000,
        speed: $('#speed').val()

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
            if ($('#speed').val() > 0) {
                $('#v-speed').show().append($('#speed').val() + ' MiB/s')
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
        console.error(`Couldn't share because of `, err.message);
    });
})

$(function () {
    const duration = new Date()
    // Set minimum datetime to now
    $('#duration').attr({"min": duration.toISOString().slice(0, -8)})
    $('#validity').attr({"min": duration.toISOString().slice(0, -8)})
    duration.setHours(duration.getHours() + 12)
    $('#validity').val(duration.toISOString().slice(0, -8))
    duration.setHours(duration.getHours() + 12)
    $('#duration').val(duration.toISOString().slice(0, -8))
});
