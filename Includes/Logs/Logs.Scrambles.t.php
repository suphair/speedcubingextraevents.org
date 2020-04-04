<h1>
    Logs scrambles
</h1>
<table class='table_new'>
    <thead>
        <tr>
            <td>
                DateTime
            </td>
            <td>
                Scrambles
            </td>
            <td>
                Status
            </td>
            <td>
                Competition
            </td>
            <td>
                Event
            </td>
            <td>
                Round
            </td>
            <td>
                Delegate
            </td>
            <td>
                Action
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row) { ?>
            <tr>
                <td><?= $row->timestamp ?></td>
                <td>
                    <?php if ($row->fileScramble) { ?>
                        <a target="_blank" href="<?= $row->fileScramble ?>">
                            <?= $row->secret ?>
                        </a>
                    <?php } else { ?>
                        <?= $row->secret ?>
                    <?php } ?>    
                </td>
                <td>
                    <span class="scramble_icon <?= $row->status ?>"></span>
                    <?= $row->status ?>
                </td>   
                <td>
                    <?= $row->competitionEvent->competition->name ?>
                </td>
                <td>
                    <?= $row->competitionEvent->event->image ?>
                    <?= $row->competitionEvent->event->name ?>
                </td>
                <td>
                    <?= $row->competitionEvent->round ?>
                </td>
                <td>
                    <a href="<?= $row->delegate->link ?>">
                        <?= $row->delegate->competitor->name ?>
                    </a>
                </td>
                <td>
                    <?= $row->action ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>