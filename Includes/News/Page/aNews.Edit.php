<?php $aNews=ObjectClass::getObject('PageaNews');
$Text=json_decode($aNews['Text'],true); ?>
<h1>News / Edit</h1>
<table class="table_info">
    <tr>
        <td></td>
        <td>The first line is the title</td>
    </tr>   
    <tr>
        <td></td>
        <td>Markdown is used</td>
    </tr> 
    <form method="POST" action="<?= PageAction('aNews.Edit') ?>">
    <input hidden name='ID' value='<?= $aNews['ID'] ?>'>
    <?php foreach(getLanguages() as $language){ ?>
    <tr>
        <td>
            <?= ImageCountry($language, 30)?>
            <?= CountryName($language,true)?>
        </td>
        <td>
            <textarea class="big_data" name="anews[<?=$language ?>]"><?= isset($Text[$language])?$Text[$language]:'' ?></textarea>
        </td>        
    </tr>    
    <?php } ?>
    <tr>
        <td></td>
        <td><button><i class="fas fa-save"></i> Save</button></td>
    </tr> 
    </form>
    <form method="POST" action="<?= PageAction('aNews.Edit') ?>" onsubmit="return confirm('Attention: Confirm the deletion.')">
    <input hidden name='Delete' value='Delete'>
    <input hidden name='ID' value='<?= $aNews['ID'] ?>'>
    <tr>
        <td></td>
        <td><button class="delete"><i class="fas fa-trash-alt"></i> Delete</button></td>
    </tr> 
    </form>
</table>