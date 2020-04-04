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

$('select[data-selected-continent]').each(function () {
    var selected = $(this).data('selected-continent');
    if (selected) {
        $(this).find('option[value="' + selected + '"]').prop('selected', true);
    }
});

$('select[data-selected-country]').each(function () {
    var selected = $(this).data('selected-country');
    if (selected) {
        $(this).find('option[value="' + selected + '"]').prop('selected', true);
    }
});

$('table[data-table-records]').each(function(){
    if($(this).find('tbody tr').length>0){
        $('#RecordsNotFound').hide();
    }else{
       $(this).hide(); 
    }
})