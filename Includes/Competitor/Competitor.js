$('[data-competitor-email]').each(function () {
    var email = $(this).data('competitor-email');
    $(this).append('<a></a>');

    var a = $(this).find('a');
    a.attr('href', 'mailto:' + email);
    a.html(email);
});


$('[data-competitor-event-select]').click(function () {
    var event = $(this).data('competitor-event-select');

    $('[data-competitor-event-block]').hide();
    $('[data-competitor-event-block = ' + event + ']').show();
    $('[data-competitor-event-select]').removeClass('event-select');
    $('[data-competitor-event-select = ' + event + ']').addClass('event-select');
    return false;
});

$('[data-competitor-events-panel] [data-competitor-event-select]').first().click();

$('.status_icon.upcoming').addClass('fas fa-hourglass-start color_light_gray');

$('[data-competitor-rank]').each(function () {
    var html = $(this).html().trim();
    if (html === '') {
        $(this).html('-');
    } else if (html < 10) {
        $(this).addClass('best-result');
    }
});

$('[data-competitor-event-block]').each(function () {
    eventRecord($(this), 'single');
    eventRecord($(this), 'average');
});

function eventRecord($this, type) {
    var values = $this.find('[data-event-' + type + ']');
    var best = false;
    $(values.get().reverse()).each(function () {
        var value = $(this).data('event-' + type + '');
        if (value) {
            if (!best || value < best) {
                best = value;
                $(this).addClass('best-result');
            }
        } else {
            $(this).addClass('unofficial-result');
        }
    });
}

$('[data-attempt-except]').each(function () {
    var html = $(this).html().trim();
    if ($(this).data('attempt-except') === 1) {
        $(this).html('(' + html + ')');
    } else {
        $(this).html(' ' + html + ' ');
    }
});

$('[data-event-record = country]').each(function(){
   $(this).html('NR');
   $(this).addClass('best-result');
});

$('[data-event-record = continent]').each(function(){
   $(this).html('CR');
   $(this).addClass('best-result');
});

$('[data-event-record = world]').each(function(){
   $(this).html('WR');
   $(this).addClass('best-result');
});