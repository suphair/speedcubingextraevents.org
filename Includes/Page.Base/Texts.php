<?php includePage('Navigator'); ?>
<h1>
    <img style="vertical-align: middle" width="40px" src="<?= PageIndex()?>Image/Icons/settings.png">
    <?= ml('Texts.Title')?>
</h1>
<div class="content">
    <?php DataBaseClass::FromTable("BlockText"); 
    foreach(DataBaseClass::QueryGenerate() as $block){ ?>
        <div class="form">
            <b><?= $block['BlockText_Name'] ?> <?= $block['BlockText_Country'] ?></b><br>
            <?= Parsedown($block['BlockText_Value']); ?><br>
            <form method="POST" action="<?= PageAction('Texts.Edit') ?>">
                <input name="Country" type="hidden" value="<?= $block['BlockText_Country'] ?>">
                <input name="Name" type="hidden" value="<?= $block['BlockText_Name'] ?>">
                <textarea name="Comment" style="height: 200px;width: 600px"><?= $block['BlockText_Value'] ?></textarea><br>
                <input type="submit" name="submit" value="Save <?= $block['BlockText_Name'] ?>">
            </form>
        </div>
    <?php } ?>
</div>    