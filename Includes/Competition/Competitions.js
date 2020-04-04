var ce_select = 'competitions_events_select';
var ce_panel = 'competitions_events_panel';
$('.' + ce_panel + ' i').on("click", function () {
    if ($(this).hasClass(ce_select)) {
        $(this).removeClass(ce_select);
    } else {
        $(this).addClass(ce_select);
    }
    reload_competitions();
});

$('.' + ce_panel + '_none').on("click", function () {
    $('.' + ce_panel + ' i').removeClass(ce_select);
    reload_competitions();
});

function reload_competitions() {
    var events = [];
    $('.' + ce_panel + ' i.' + ce_select).each(function () {
        $(this).attr('class').split(' ').forEach(
                (element) => {
            var tmp = element.replace('ee-', '');
            if (tmp !== element) {
                events.push(tmp);
            }
        }
        );
    });
    $('.competition').removeClass('even');
    $('.competition').removeClass('odd');
    if (events.length > 0) {
        $('.' + ce_panel + '_none').show();
        $('.competition').hide();
        var i = 1;
        $('.competition').each(function () {
            var show = false;
            events.forEach(
                    (element) => {
                if ($(this).hasClass(element)) {
                    show = true;
                }
            });
            if (show) {
                $(this).show();
                if (i % 2 !== 0) {
                    $(this).addClass('odd');
                } else {
                    $(this).addClass('even');
                }
                i = i + 1;
            }
        });
        if (i === 1) {
            $('#competitionsNotFound').show();
        } else {
            $('#competitionsNotFound').hide();
        }
    } else {
        $('.' + ce_panel + '_none').hide();
        $('.competition').show();
        var i = 1;
        $('.competition').each(function () {
            if (i > 1) {
                $('#competitionsNotFound').show();
            } else {
                $('#competitionsNotFound').hide();
            }
        });
    }
}

$('[data-competitions-filter]').change(function () {
    var location = [];
    $('[data-competitions-filter]').each(function () {
        var value = $(this).val();
        if (value) {
            location.push($(this).data('competitions-filter') + '=' + value);
        }
    });
    document.location = $('#variables').data('index') + 'Competitions/?' + location.join('&');
});


$('[data-competitions-filter-mine]').click(function () {
    document.location = $('#variables').data('index') + 'Competitions/mine';
});


$('[data-competitions-filter-all]').click(function () {
    document.location = $('#variables').data('index') + 'Competitions';
});

var filter = false;
$('[data-competitions-filter-mine=1]').each(function () {
    $(this).addClass('select');
    filter = true;
});
$('[data-competitions-filter][data-selected]').each(function () {
    if ($(this).data('selected')) {
        filter = true;
    }
})
if (filter === false) {
    $('[data-competitions-filter-all]').addClass('select');
}


$('.Competition').filter(function () {
    return $(this).data('icon') !== undefined;
}).each(function () {
    alert($(this).data('icon'));
});

