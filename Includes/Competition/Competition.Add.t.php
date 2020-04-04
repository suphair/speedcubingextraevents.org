<h1>Competition / Add</h1>
<table class="table_info">
    <form method="POST" action="<?= PageAction('Competition.Add') ?>">
        <tr>
            <td>Competition ID</td>
            <td><input required type="text" name="WCA" value="" /></td>
        </tr>    
        <?php if ($data->competitionAddExt) { ?>
            <tr>
                <td>Delegate SEE <i class="fas fa-crown"></i></td>
                <td>
                    <select name="Delegate" data-selected="<?= $data->delegate->id ?>">
                        <?php foreach ($data->delegates as $delegate) { ?>
                            <option value="<?= $delegate->id ?>">
                                <?= $delegate->name ?> (<?= $delegate->status ?>)
                            </option>
                        <?php } ?>
                    </select>           
                </td>
            </tr>   
        <?php } else { ?>
            <tr>
                <td>Delegate SEE</td>
                <td><?= $data->delegate->name ?></td>
            </tr>   
        <?php } ?>
        <tr>
            <td></td>
            <td><button><i class="fas fa-plus-square"> Create</button></td>
        </tr>
    </form>
    <?php if ($data->error) { ?>
        <tr>
            <td><i class="fas fa-exclamation-triangle"></i></td>
            <td class="color_red"><?= $data->error ?></td>
        </tr>    
    <?php } ?>
</table>