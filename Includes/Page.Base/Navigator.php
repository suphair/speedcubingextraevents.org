<?php $type = RequestClass::getPage();
if(explode('.',$type)[0]=='aNews')$type='News';
if(explode('.',$type)[0]=='Delegate' or explode('.',$type)[0]=='Delegates')$type='Delegates';
if(explode('.',$type)[0]=='Competitor')$type='Competitors';
if(explode('.',$type)[0]=='Competition')$type='Competitions';
if(explode('.',$type)[0]=='Event')$type='Events';
?>
<div class="Navigator">
        <nobr><a <?= ($type=='Competitions' or $type=='index')?"class='select'":"" ?> href="<?= PageIndex() ?>Competitions">
        <i class="fas fa-cube"></i> <?= ml('Navigator.Competitions') ?></a></nobr>&nbsp;&nbsp;&nbsp;
        <nobr><a <?= ($type=='Regulations')?"class='select'":"" ?>  href="<?= PageIndex() ?>Regulations">
        <i class="fas fa-book"></i> <?= ml('Navigator.Regulations') ?></a></nobr>&nbsp;&nbsp;&nbsp;
        <nobr><a <?= ($type=='Records')?"class='select'":"" ?>  href="<?= PageIndex() ?>Records">
        <i class="fas fa-trophy"></i> <?= ml('Navigator.Records') ?></a></nobr>&nbsp;&nbsp;&nbsp;
        <nobr><a <?= ($type=='Competitors')?"class='select'":"" ?>  href="<?= PageIndex() ?>Competitors">
        <i class="fas fa-users"></i> <?= ml('Navigator.Competitors') ?></a></nobr>&nbsp;&nbsp;&nbsp;
        <nobr><a <?= ($type=='Events')?"class='select'":"" ?>  href="<?= PageIndex() ?>Events">
        <i class="fas fa-star"></i> <?= ml('Navigator.Events') ?></a></nobr>&nbsp;&nbsp;&nbsp;
        <nobr><a <?= ($type=='Delegates')?"class='select'":"" ?>  href="<?= PageIndex() ?>Delegates">
        <i class="fas fa-sitemap"></i> <?= ml('Navigator.Delegates') ?></a></nobr>&nbsp;&nbsp;&nbsp;
        <nobr><a <?= ($type=='News')?"class='select'":"" ?>  href="<?= PageIndex() ?>News">
        <i class="far fa-newspaper"></i> <?= ml('Navigator.News') ?></a></nobr>&nbsp;&nbsp;&nbsp;       
</div>

