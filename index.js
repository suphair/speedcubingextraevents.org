$('select[data-selected]').each(function () {
    var selected = $(this).data('selected');
    $(this).find('option[value="' + selected + '"]').prop('selected', true);
});


$('[data-block-name]').each(function () {
    var name = $(this).data('block-name');
    $(this).appendTo('[data-block-from=' + name + ']');
    $(this).removeClass('hidden');
});

$('[data-confirm-delete]').submit(function () {
    return confirm('Attention: Confirm the deletion.');
});

$('button[data-href]').click(function () {
    document.location.href = $(this).data('href');
});

$('[data-unnoficial=1]').addClass('unofficial');

$('[data-set-title]').each(function () {
    var titles = $(this).data('set-title').split(',');
    titles.push($('#variables').data('title'));
    document.title = titles.join(" " + String.fromCharCode(9642) + " ");
});

$('[data-location]').html(document.location.href);

$('[data-hidden = 1]').hide();

$('[data-selected = 1]').addClass('selected');

$('[data-selected-value]').each(function () {
    var value = $(this).data('selected-value');
    $(this).find('[data-selected-condition=' + value + ']').addClass('selected');

});

$('[data-hidden-href-empty]').each(function () {
    if ($(this).find('a').attr('href') === '') {
        $(this).hide();
    }
});


$('a[data-external-link]').each(function () {
    $(this).attr('target', '_blank');
    $(this).append('<i class="fas fa-external-link-alt"></i>');
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

$('[data-attempt-except]').each(function () {
    var html = $(this).html().trim();
    if ($(this).data('attempt-except') === 1) {
        $(this).html('(' + html + ')');
    } else {
        $(this).html("\u00A0" + html + "\u00A0");
    }
});


var date = new Date();
                            var options = {
  year: 'numeric',
  month: 'long',
  day: 'numeric',
  timezone: 'UTC',
  timeZoneName: 'short',
  hour: '2-digit',
  minute: '2-digit',
  second: '2-digit',
  hour12: false
};
var date_str=date.toLocaleString("en-US", options);

$('a[data-add-date]').click(function () {
    $(this).attr('href',$(this).attr('href') + '/?date=' + date_str);
});

$('form[data-add-date]').submit(function () {
    $(this).append('<input hidden value="' + date_str +'" name="date">');
});