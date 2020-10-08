<h1>
    <i class="fas fa-user-slash"></i>
    Banned members
</h1>
<table class="table_new">
    <thead>
        <tr>
            <td>
                WCA ID
            </td>
            <td>
                Start date
            </td>
            <td>
                End date
            </td>
            <td>
                Reason
            </td>
        </tr>
    </thead>
    <?php
    $ban_users = ban::get_db_list(DataBaseClass::getConection());
    foreach ($ban_users as $ban_user) {
        ?>
        <tr>
            <td>
                <a data-external-link
                   href="https://www.worldcubeassociation.org/persons/<?= $ban_user->wca_id ?>">
                       <?= $ban_user->wca_id ?>
                </a>
            </td>
            <td>
                <?= $ban_user->start_date ?>
            </td>
            <td>
                <?= $ban_user->end_date ?>
            </td>
            <td>
                <?= $ban_user->reason ?>
            </td>
        </tr>
    <?php } ?>
</table>
