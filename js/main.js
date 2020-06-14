$(document).ready(function () {

});

$('form').submit(function (e) {
    // Prevent browser from connecting itself to action path
    e.preventDefault();

    let data;
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
    $('#form').toggle();
    $('#approval').toggle();
});

function displayView(view) {
    const views = ['#form', '#approval', '#loading', '#error', '#success']
    if (!views.includes(view)) {
        return false;
    }
    views.forEach(v => {
        if (v != view) {
            $(v).hide();
        } else {
            $(v).show();
        }
    });
}