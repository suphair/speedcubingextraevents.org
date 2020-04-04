<h1>Logs cron</h1>
<table class="table_new">
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
        <?php foreach ($data as $row) { ?>
            <tr>
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
