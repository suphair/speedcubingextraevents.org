<h1>
    <a href="<?= $data->link ?>">
        <?= $data->competitor->name ?>
    </a>
    / Settings
</h1>
<table class="table_info">
    <?php if ($data->contact) { ?>    
        <tr>
            <td>
                Contacts to display
            </td>
            <td>
                <?= Parsedown($data->contact) ?>
            </td>
        </tr>    
    <?php } ?>    
    <form method="POST" action="<?= PageAction("Delegate.Edit") ?>">
        <input name="ID" type="hidden" value="<?= $data->id ?>" />
        <tr>
            <td>
                Contacts
            </td>
            <td>
                <textarea class="big_data" name="Contact"><?= $data->contact ?></textarea>
                <p>
                    for email - &lt;mail@example.com&gt;
                </p>
                <p>
                    for link - [link name](example.com)
                </p>
            </td>
        </tr>   
        <?php if ($data->accessSettingExt) { ?>
            <tr>
                <td>
                    Status
                    <i class="fas fa-crown"></i>
                </td>
                <td>
                    <select name="Status" data-selected="<?= $data->status ?>">
                        <?php foreach ($data->statuses as $status) { ?>
                            <option value="<?= $status ?>">
                                <?= $status ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>    
        <?php } else { ?>
            <tr>
                <td>
                    Status
                </td>
                <td>
                    <?= $data->status ?>
                </td>
            </tr>  
        <?php } ?>
        <tr>
            <td>
                Secret for alternative login
            </td>
            <td>
                <input name="Secret" value="<?= $data->secret ?>" />
            </td>
        </tr>  
        <tr>
            <td/>
            <td>
                <button>
                    <i class="far fa-save"></i>
                    Save
                </button>
            </td>
        </tr>  
    </form>
</table>

<?php if ($data->availableForDeletion) { ?>
    <form method="POST" action="<?= PageAction("Delegate.Delete") ?>" data-confirm-delete >
        <input name="ID" type="hidden" value="<?= $data->id ?>" />
        <button class="delete">
            <i class="fas fa-trash-alt"></i>
            Delete
        </button>
    </form>
<?php } ?> 