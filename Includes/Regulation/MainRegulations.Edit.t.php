<p>
    <i class='fas fa-book'></i>
    <a href="<?= PageIndex() ?>MainRegulations">
        Main regulations
    </a>
</p>
<table class='main_regulation_edit'>
    <tr>
        <td>
            <h2>
                <i class="fas fa-edit"></i>
                Edit
            </h2>
            <form method="POST" action="<?= PageAction('MainRegulation.Edit') ?>">
                <input hidden value="<?= $data->language ?>" name="language">    
                <textarea class='edit' name="text"><?= $data->regulationsEdit ?></textarea>
                <button>
                    <i class="far fa-save"></i>
                    Save
                </button>
            </form>
        </td>   
        <td>
            <h2>
                <i class="far fa-eye"></i>
                View
            </h2>
            <div class='view'>
                <?= $data->regulations ?>
            </div>
        </td>   
    </tr>
</table>