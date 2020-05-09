<h1>
    Competition / Add
</h1>
<table class='table_info'>
    <form method='POST' action='<?= PageAction('Competition.Add') ?>'>
        <tr>
            <td>
                Competition ID
            </td>
            <td>
                <input required type='text' name='WCA' value='' />
            </td>
        </tr>    

        <tr data-hidden = '<?= !$data->competitionAddExt ?>' >
            <td>
                Delegate SEE
                <i class='fas fa-crown'></i>
            </td>
            <td>
                <select name='Delegate' data-selected='<?= $data->delegate->id ?>'>
                    <?php foreach ($data->delegates as $delegate) { ?>
                        <option value='<?= $delegate->id ?>'>
                            <?= $delegate->competitor->name ?>
                            (<?= $delegate->status ?>)
                            <?= $delegate->competitor->country->name ?>
                        </option>
                    <?php } ?>
                </select>           
            </td>
        </tr>   

        <tr data-hidden = '<?= $data->competitionAddExt ?>' >
            <td>
                Delegate SEE
            </td>
            <td>
                <?= $data->delegate->name ?>
            </td>
        </tr>   

        <tr>
            <td></td>
            <td>
                <button>
                    <i class='fas fa-plus-square'></i>
                    Create
                </button>
            </td>
        </tr>
    </form>
    <tr data-hidden = '<?= empty($data->error) ?>' >
        <td>
            <i class='fas fa-exclamation-triangle'></i>
        </td>
        <td class='color_red'>
            <?= $data->error ?>
        </td>
    </tr>    
</table>