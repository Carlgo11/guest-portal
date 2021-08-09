$(document).ready(function () {
    // $('form').show();
});

function displayView(view) {
    $('.view').hide();
    $(view).show();
}

$('.btn-back').click(function () {
    location.reload();
});
