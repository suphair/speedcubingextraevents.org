<?php includePage('Navigator'); ?>

<?php $aNews=ObjectClass::getObject('PageaNews');
$Text=json_decode($aNews['Text'],true);
?>
<h1><?= ml('aNews.Edit.Title') ?></h1>
The first line is the title.<br>
HTML tags are allowed<br>
The newline is replaced with <br>
<div class="wrapper">
    <div class="form">
        <b><?= $aNews['Name']?></b> ▪ <?= $aNews['Date']?>
        <form method="POST" action="<?= PageAction('aNews.Edit') ?>">
            <?php foreach(getLanguages() as $language){ ?>
            <div class="form_input form_input_left">        
                <?= ImageCountry($language, 30)?>
                <b><?= CountryName($language,true)?></b><br>
                <textarea name="anews[<?=$language ?>]"><?= isset($Text[$language])?$Text[$language]:'' ?></textarea>
            </div>
            <?php } ?>
            <div>
                <input hidden name='ID' value='<?= $aNews['ID'] ?>'>
                <input type="submit" value="Save">
                <input type="submit" name='Delete' class="delete" value="Delete" onclick="return confirm('Attention: Confirm delete a news.')">
            </div>
        </form>
    </div>
</div>
