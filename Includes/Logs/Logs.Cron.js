$('[data-select-cron-object]').change(function () {
    var object = $(this).val();
    if (object === 'all') {
        $('[data-table-cron-object] tbody tr').show('fast');
    } else {
        $('[data-table-cron-object] tbody tr').hide();

        var i = 1;
        $('[data-table-cron-object] tbody tr[data-cron-object=' + object + ']').each(function () {
            $(this).show();
            if (i % 2 !== 0) {
                $(this).addClass('odd');
            } else {
                $(this).addClass('even');
            }
            i = i + 1;
        });
    }
});

