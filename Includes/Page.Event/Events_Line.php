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
    <?php foreach($disciplines as $discipline_row){ ?>   
        <a class="<?= $discipline_row['Discipline_ID']==$Event_line['Discipline_ID']?"line_select":""?>" title="<?= $discipline_row['Discipline_Name'] ?>" href="<?= LinkDiscipline($discipline_row['Discipline_Code']) ?><?= $Settings?"/Settings":"" ?>"><?= ImageDiscipline($discipline_row['Discipline_CodeScript'],50) ?></a> 
    <?php } ?> 
</div>