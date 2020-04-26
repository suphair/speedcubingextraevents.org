$('[data-regulations-event]').change(function () {
    location.href = $('#variables').data('index') + 'Regulations/' + $(this).val();
});

var competitionevent = $('[data-competitionevent]').data('competitionevent');
$('[data-competitionevent-list=' + competitionevent +']').addClass('select');
