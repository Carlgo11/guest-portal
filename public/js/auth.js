$(document).ready(function () {
    const usp = new URLSearchParams(window.location.search);
    if (usp.get('ssid') != null) {
        let ssid = decodeURIComponent(usp.get('ssid').replace(/\+/g, ' '));
        $('.title').text(ssid + " WiFi");
        document.title = decodeURIComponent(ssid);
    }
});

$('#manual').click(function () {
    displayView('#approval');
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