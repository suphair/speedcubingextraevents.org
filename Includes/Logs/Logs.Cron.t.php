<h1>Logs cron</h1>

<table class="table_info">
    <tr>
        <td>
            Object
        </td>
        <td>
            <select data-select-cron-object>
                <option value='all' >All</option>
                <?php foreach($data->objects as $object){ ?>
                <option value='<?= $object ?>'><?= $object ?></option>
                <?php } ?>
                </select>
        </td>
        </tr>
    </table>

<table class="table_new" data-table-cron-object>
    <thead>
        <tr>
            <td>
                DateTime
            </td>
            <td>
                Cron
            </td>
            <td>
                Details
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data->logs as $row) { ?>
            <tr data-cron-object='<?= $row->object ?>'>
                <td>
                    <?= $row->timestamp ?>
                </td>
                <td>
                    <?= $row->object ?>
                </td>
                <td>
                    <?= $row->details ?>
                </td>
            </tr>
        <?php } ?>         
    <tbody>
</table>
