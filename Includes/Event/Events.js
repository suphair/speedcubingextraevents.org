$('[data-events-filter]').change(function () {
    document.location = $('#variables').data('index') + 'Events/' + $(this).val();
});

$('[data-event-archive = 1]').addClass('fas fa-ban');

$('table[data-access-event-settings != 1] [data-event-settings]').hide();

$('[data-event-isTeam = 0]').hide();

$('[data-event-show = 0 ]').hide();

