$('[data-records-request]').change(function () {
    var requests = [];
    $('[data-records-request]').each(function () {
        var value = $(this).val();
        var field = $(this).data('records-request');
        if (value !== '') {
            if (value.includes('continent_')) {
                value = value.replace('continent_', '');
                field = 'continent';
            }
            requests.push(field + '=' + value.toLowerCase());
        }
    });
    document.location = $('#variables').data('index') + 'records/?' + requests.join('&');
});

$('table[data-table-records]').each(function () {
    if ($(this).find('tbody tr').length > 0) {
        $('#RecordsNotFound').hide();
    } else {
        $(this).hide();
    }
})


var events = [];
$('[data-record-single]').each(function () {
    var event = $(this).data('record-single');
    if ($.inArray(event, events)) {
        events.push(event);
    }
});

events.forEach(function (item) {
    $('[data-record-single=' + item + ']').first().addClass('best-result');
    $('[data-record-average=' + item + ']').first().addClass('best-result');
});
    