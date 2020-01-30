<?php $type = RequestClass::getPage();
if(explode('.',$type)[0]=='aNews')$type='News';
if(explode('.',$type)[0]=='Delegate' or explode('.',$type)[0]=='Delegates')$type='Delegates';
if(explode('.',$type)[0]=='Competitor')$type='Competitors';
if(explode('.',$type)[0]=='Event')$type='Events';
?>
<div class="Navigator">
        <nobr><a <?= ($type=='Competitions' or $type=='index')?"class='select'":"" ?> href="<?= PageIndex() ?>Competitions">
        <img src='<?= PageIndex()?>Image/Icons/competitions.png' width='15px'> <?= ml('Navigator.Competitions') ?></a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Regulations')?"class='select'":"" ?>  href="<?= PageIndex() ?>Regulations">
        <img src='<?= PageIndex()?>Image/Icons/regulation.png' width='15px'> <?= ml('Navigator.Regulations') ?></a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Records')?"class='select'":"" ?>  href="<?= PageIndex() ?>Records">
        <img src='<?= PageIndex()?>Image/Icons/record.png' width='15px'>  <?= ml('Navigator.Records') ?></a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Competitors')?"class='select'":"" ?>  href="<?= PageIndex() ?>Competitors">
        <img src='<?= PageIndex()?>Image/Icons/persons.png' width='15px'> <?= ml('Navigator.Competitors') ?></a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Events')?"class='select'":"" ?>  href="<?= PageIndex() ?>Events"><?= ml('Navigator.Events') ?></a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Delegates')?"class='select'":"" ?>  href="<?= PageIndex() ?>Delegates"><?= ml('Navigator.Delegates') ?></a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='News')?"class='select'":"" ?>  href="<?= PageIndex() ?>News"><?= ml('Navigator.News') ?></a> <span style="color:var(--back_color)">|</span></nobr>        
</div>
<hr>