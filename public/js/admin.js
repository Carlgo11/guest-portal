$(document).ready(function () {
    $.ajax({
        type: 'GET',
        url: '/_api/guests.php',
        success: function (res) {
            //console.info(res);
            res.forEach(client => {
                if (client['authorized']) {
                    addOnline(client);

                } else {
                    addRequest(client)
                }
            });
        },
        error: function (res) {
            console.error(res);
        }
    });
});

function addRequest(client) {
    let req = $('#request').clone();
    req.prop('id', client['mac']);
    req.prepend(client['hostname']);
    req.show();
    req.appendTo('#requests');
}

function addOnline(client) {
    let online = $('li#online').clone();
    online.prop('id', client['mac']);
    online.prepend(client['hostname']);
    online.show();
    online.appendTo('#online');
}