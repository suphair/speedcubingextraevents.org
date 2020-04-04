<h1>
    Logs authorisations
</h1>
<table class='table_new'>
    <thead>
        <tr>
            <td>
                DateTime
            </td>
            <td>
                Competitor
            </td>
            <td>
                Action
            </td>
            <td>
                Country
            </td>
            <td>
                WCA ID
                <i class="fas fa-external-link-alt"></i>
            </td>
            <td>
                WID
                <i class="fas fa-external-link-alt"></i>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $log) { ?>
            <tr>
                <td>
                    <?= $log->timestamp ?>
                </td>
                <td>
                    <a href="<?= $log->competitor->link ?>">
                        <?= $log->competitor->name ?>
                    </a>
                </td>
                <td>
                    <i class="status_log <?= ($log->action) ?>"></i>
                    <?= ($log->action) ?>
                </td>
                <td>
                    <?= $log->competitor->country->image ?>
                    <?= $log->competitor->country->name ?>
                </td>
                <td>
                    <a target="_blank" href="<?= $log->competitor->linkWca ?>">
                        <?= $log->competitor->wcaid ?>
                    </a>
                </td>
                <td class="table_new_right">
                    <a target="_blank" href="<?= $log->competitor->linkApiUser ?>">
                        <?= $log->competitor->wid ?>
                    </a>
                </td>
            <?php } ?>    
    </tbody>    
</table>
