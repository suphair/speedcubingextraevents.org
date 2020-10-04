
addEventListener("keydown", function (e) {
    switch (e.keyCode) {
        case 32:
            e.preventDefault();
            location.reload();
            break;
    }
});
$('[data-event-select]').change(function () {
    var event = $(this).val();
    document.location = $('#variables').data('index') + 'event/' + event + '/training';
});