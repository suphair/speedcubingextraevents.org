<?php $type = RequestClass::getPage();
if(explode('.',$type)[0]=='aNews')$type='News';
if(explode('.',$type)[0]=='Delegate' or explode('.',$type)[0]=='Delegates')$type='Delegates';
if(explode('.',$type)[0]=='Competitor')$type='Competitors';
if(explode('.',$type)[0]=='Event')$type='Events';
?>
<div class="Navigator">
        <nobr><a <?= ($type=='Competitions' or $type=='index')?"class='select'":"" ?> href="<?= PageIndex() ?>Competitions">
        <img src='<?= PageIndex()?>Image/Icons/competitions.png' width='15px'> <?= ml('Navigator.Competitions') ?></a></nobr>&nbsp;
        <nobr><a <?= ($type=='Regulations')?"class='select'":"" ?>  href="<?= PageIndex() ?>Regulations">
        <img src='<?= PageIndex()?>Image/Icons/regulation.png' width='15px'> <?= ml('Navigator.Regulations') ?></a></nobr>&nbsp;
        <nobr><a <?= ($type=='Records')?"class='select'":"" ?>  href="<?= PageIndex() ?>Records">
        <img src='<?= PageIndex()?>Image/Icons/record.png' width='15px'>  <?= ml('Navigator.Records') ?></a></nobr>&nbsp;
        <nobr><a <?= ($type=='Competitors')?"class='select'":"" ?>  href="<?= PageIndex() ?>Competitors">
        <img src='<?= PageIndex()?>Image/Icons/persons.png' width='15px'> <?= ml('Navigator.Competitors') ?></a></nobr>&nbsp;
        <nobr><a <?= ($type=='Events')?"class='select'":"" ?>  href="<?= PageIndex() ?>Events">
        <img src='<?= PageIndex()?>Image/Icons/SEE.png' width='15px'> <?= ml('Navigator.Events') ?></a></nobr>&nbsp;
        <nobr><a <?= ($type=='Delegates')?"class='select'":"" ?>  href="<?= PageIndex() ?>Delegates">
        <img src='<?= PageIndex()?>Image/Icons/delegates.png' width='15px'> <?= ml('Navigator.Delegates') ?></a></nobr>&nbsp;
        <nobr><a <?= ($type=='News')?"class='select'":"" ?>  href="<?= PageIndex() ?>News">
        <img src='<?= PageIndex()?>Image/Icons/news.png' width='15px'> <?= ml('Navigator.News') ?></a></nobr>&nbsp;       
</div>
<hr>