
$('[data-event-request]').change(function () {
    var event;
    var format;
    var requests = [];
    $('[data-event-request]').each(function () {
        var value = $(this).val();
        var field = $(this).data('event-request');
        if (value !== '') {
            if (value.includes('continent_')) {
                value = value.replace('continent_', '');
                field = 'continent';
            }
            if (field === 'event') {
                event = value.toLowerCase();
            } else if (field === 'format') {
                format = value.toLowerCase();
            } else {
                requests.push(field + '=' + value.toLowerCase());
            }
        }
    });
    document.location = $('#variables').data('index') + 'event/' + event + '/' + format + '?' + requests.join('&');
});

var number = 1;
var rank = 1;
var value = false;
$('[data-event-result-value]').each(function () {
    var result = $(this).data('event-result-value');
    if (value === false) {
        value = result;
    } else {
        if (result > value) {
            value = result;
            rank = number;
        } else {
            $(this).addClass('color_light_gray');
        }
    }
    number = number + 1;
    $(this).html(rank);
});

if ($('[data-event-results] tbody tr').length > 0) {
    $('#resutsNotFound').hide();
}
