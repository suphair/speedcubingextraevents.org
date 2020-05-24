<h1>Logs cron</h1>

<table class="table_info">
    <tr>
        <td>
            <i class="fas fa-filter">
            Filter
        </td>
        <td>
            <select data-select-cron-name>
                <option value='all' >All</option>
                <?php foreach ($data->names as $name) { ?>
                    <option value='<?= $name ?>'><?= $name ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<table class="table_new" data-table-cron-name>
    <thead>
        <tr>
            <td>
                Name
            </td>
            <td>
                Start
            </td>
            <td>
                End
            </td>
            <td>
                Details
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data->logs as $row) { ?>
            <tr data-cron-name='<?= $row->cronName ?>'>
                <td>
                    <?= $row->cronName ?>
                </td>
                <td>
                    <?= $row->cronStart ?>
                </td>
                <td>
                    <?= $row->cronEnd ?>
                </td> 
                <td>
                    <?= $row->details ?>
                </td>
            </tr>
        <?php } ?>         
    <tbody>
</table>