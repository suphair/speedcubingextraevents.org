$('#competitor-find').on("input", function () {
    var find = $(this).val().toLowerCase();
    reload(find);
});

function reload(find) {
    var i = 1;
    var competitors = $('table[data-competitors] tbody tr');

    competitors.hide();
    competitors.removeClass('even');
    competitors.removeClass('odd');
    competitors.each(function () {
        var key = $(this).data('key').toLowerCase();
        if (key.indexOf(find) !== -1) {
            $(this).show();
            if (i % 2 !== 0) {
                $(this).addClass('odd');
            } else {
                $(this).addClass('even');
            }
            i = i + 1;
        }
    });
}

$('[data-competitors-request]').change(function () {
    var requests = [];
    $('[data-competitors-request]').each(function () {
        var value = $(this).val();
        var field = $(this).data('competitors-request');
        if (value !== '') {
            requests.push(field + '=' + value);
        }
    });
    document.location = $('#variables').data('index') + 'competitors/?' + requests.join('&');
});
