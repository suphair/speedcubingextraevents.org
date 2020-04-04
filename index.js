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

$('tr[data-hidden = 1]').hide();

$('[data-selected = 1]').addClass('selected');

$('[data-selected-value]').each(function () {
    var value = $(this).data('selected-value');
    $(this).find('[data-selected-condition=' + value + ']').addClass('selected');

})