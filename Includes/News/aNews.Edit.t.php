<h1>
    <a href="<?= PageIndex() ?>/news">
        News
    </a>
</h1>
<table class="table_info">
    <tr>
        <td></td>
        <td>The first line is the title</td>
    </tr>   
    <tr>
        <td></td>
        <td>Markdown is used</td>
    </tr> 
    <form method="POST" 
    <?php if ($data->id) { ?>
              action="<?= PageAction('aNews.Edit') ?>"
          <?php } else { ?>
              action="<?= PageAction('aNews.Add') ?>"
          <?php } ?>
          >
        <input hidden name='ID' value='<?= $data->id ?>'>
        <?php foreach ($data->languages as $language) { ?>
            <tr>
                <td>
                    <?= $language->image ?>
                    <?= $language->name ?>
                </td>
                <td>
                    <textarea class="big_data" name="anews[<?= $language->code ?>]"><?= $language->text ?></textarea>
                </td>        
            </tr>    
        <?php } ?>
        <tr>
            <td/>
            <td>
                <button>
                    <?php if ($data->id) { ?>
                        <i class="fas fa-save"></i>
                        Save
                    <?php } else { ?>
                        <i class="fas fa-plus-circle"></i>
                        Create
                    <?php } ?>

                </button>
            </td>
        </tr> 
    </form>
    <?php if ($data->id) { ?>
        <form method="POST" action="<?= PageAction('aNews.Delete') ?>" data-confirm-delete>
            <input hidden name='ID' value='<?= $data->id ?>'>
            <tr>
                <td/>
                <td>
                    <button class="delete">
                        <i class="fas fa-trash-alt"></i>
                        Delete
                    </button>
                </td>
            </tr> 
        </form>
    <?php } ?>
</table>