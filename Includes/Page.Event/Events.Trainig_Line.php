<?php
$Request= Request();
$Settings=(isset($Request[2]) and $Request[2]=='settings');
$Event_line = ObjectClass::getObject('PageEvent');

DataBaseClass::FromTable('Discipline'); 
DataBaseClass::OrderClear('Discipline','Name'); 
DataBaseClass::Where_current("Status='Active'");    
$disciplines=DataBaseClass::QueryGenerate();
?>
<div class="line">
    <h2>
        Training scrambling
    <?php foreach($disciplines as $discipline_row){ ?>   
        <?php if(
                (file_exists("Functions/Generate_{$discipline_row['Discipline_CodeScript']}.php")  or
                file_exists("Functions/GenerateTraining_{$discipline_row['Discipline_CodeScript']}.php") or
                file_exists("Script/{$discipline_row['Discipline_CodeScript']}_generator.js"))
                and file_exists("Scramble/{$discipline_row['Discipline_CodeScript']}.php")){ ?>
        <a class="<?= $discipline_row['Discipline_ID']==$Event_line['Discipline_ID']?"line_select":""?>" title="<?= $discipline_row['Discipline_Name'] ?>" href="<?= LinkDiscipline($discipline_row['Discipline_Code']) ?>/Training"><?= ImageEvent($discipline_row['Discipline_CodeScript'],25) ?></a> 
        <?php } ?> 
    <?php } ?>
    </h2>
</div>