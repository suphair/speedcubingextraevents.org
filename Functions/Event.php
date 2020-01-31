<?php

function EventRoundView($Competition=0){
    
        DataBaseClass::FromTable("Event");
        if($Competition){
            DataBaseClass::Where_current("Competition=$Competition");    
        }
        DataBaseClass::OrderClear("Event", "Round");
        $events=array();
        foreach(DataBaseClass::QueryGenerate() as $event){
            $events[$event['Event_Competition']][$event['Event_DisciplineFormat']]=$event['Event_Round'];
        }
        
        foreach(DataBaseClass::QueryGenerate() as $event){     
            if($events[$event['Event_Competition']][$event['Event_DisciplineFormat']]>1){
                $round_out=': '.array('1'=>'1st','2'=>'2nd','3'=>'3rd','4'=>'4th')[$event['Event_Round']].' round';
            }else{
                $round_out="";
            }
                
            DataBaseClass::Query("Update Event set vRound='$round_out' where ID=".$event['Event_ID']);
        }
}


function EventBlockLinks($Event,$current=""){
    ob_start();  ?>
    
    <div class="block_comment">
        <?php if(CheckAccess('Event.Settings')){ ?>
            <a class="<?= $current=='settings'?'select':''?>" href="<?= LinkDiscipline($Event['Discipline_Code'])?>/Settings"><img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?></a>
        <?php } ?>
            
        <?php $status=(isset($Event['Discipline_Status']) and $Event['Discipline_Status']!='Archive'); ?>
        <?php if($status){ ?>    
            <a class="<?= $current=='regulations'?'select':''?>" href="<?= PageIndex()?>Regulations#<?= $Event['Discipline_Code'] ?>"> <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/regulation.png"> <?= ml('Competition.Regulation'); ?></a>    
        <?php } ?>
        <a class="<?= $current=='records'?'select':''?>" href="<?= PageIndex()?>Records/all/<?= $Event['Discipline_Code'] ?>"> <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/record.png"> <?= ml('Event.Records'); ?></a>
        <a class="<?= $current=='rankings'?'select':''?>" href="<?= LinkDiscipline($Event['Discipline_Code'])?>"> <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/rankings.png"><?= ml('Competition.Rankings'); ?></a>
        <?php if($status){ ?>    
            <?= scramble_block($Event['Discipline_ID']);?>
            <?= scorecard_block($Event['Discipline_ID']);?>
        <?php } ?>
        <?php
        $exists_GenerateTraining=file_exists("Functions/GenerateTraining_{$Event['Discipline_CodeScript']}.php");
        $exists_Generate=file_exists("Functions/Generate_{$Event['Discipline_CodeScript']}.php");
        $exists_ScriptGenerate=file_exists("Script/{$Event['Discipline_CodeScript']}_generator.js");
        if($exists_GenerateTraining or $exists_Generate or  $exists_ScriptGenerate){ ?>
            <a class="<?= $current=='training'?'select':''?>" href="<?= PageIndex()?>Event/<?= $Event['Discipline_Code'] ?>/Training"> <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/scramble.png"> <?= ml('TrainingScrambling.Title') ?></a>
            
        <?php } ?>
    </div><br>
<?php $return = ob_get_contents();
    ob_end_clean();
    return "<nobr>$return</nobr>";
}