<?php includePage('Navigator'); ?>
<h1><?= ml('aNews.AddTitle') ?></h1>
The first line is the title.<br>
HTML tags are allowed<br>
The newline is replaced with <br>

<div class="wrapper">
    <div class="form">
        <form method="POST" action="<?= PageAction('aNews.Add') ?>">
            <?php foreach(getLanguages() as $language){ ?>
            <div class="form_input form_input_left">        
                <?= ImageCountry($language, 30)?>
                <b><?= CountryName($language,true)?></b><br>
                <textarea name="anews[<?=$language ?>]"></textarea>
            </div>
            <?php } ?>
            <div>
                <input type="submit" value="<?= ml('*.Add',false) ?>">
            </div>
        </form>
    </div>
</div>

<?= mlb('*.Add') ?>
