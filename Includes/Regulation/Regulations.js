$('[data-regulations-event]').change(function () {
    location.href = $('#variables').data('index') + 'Regulations/' + $(this).val();
});

var event = $('[data-regulations-event]').data('selected');
$('[data-regulations-event-list=' + event +']').addClass('select');
