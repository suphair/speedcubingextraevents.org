$('[data-select-cron-name]').change(function () {
    var name = $(this).val();
    if (name === 'all') {
        $('[data-table-cron-name] tbody tr').show('fast');
    } else {
        $('[data-table-cron-name] tbody tr').hide();

        var i = 1;
        $('[data-table-cron-name] tbody tr[data-cron-name=' + name + ']').each(function () {
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

