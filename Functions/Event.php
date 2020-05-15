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


function EventBlockLinks($Event,$current="",$table_exists=false){
    ob_start();  ?>
    
    <?php if(!$table_exists){ ?><table class="table_info"><?php } ?>
        <?php if(CheckAccess('Event.Settings')){ ?>
            <tr>
                <td><i class="fas fa-crown"></i></td>
                <td>
                    <?php if($current=='settings'){ ?>
                        Main event setting
                    <?php }else{  ?>
                        <a href="<?= LinkDiscipline($Event['Discipline_Code'])?>/Settings">Main event setting</a>
                    <?php } ?>
                </td>
            </tr>    
        <?php } ?>
        <?php $status=(isset($Event['Discipline_Status']) and $Event['Discipline_Status']!='Archive'); ?>
        <?php if($status){ ?>   
            <tr>  
                <td><i class="fas fa-book"></i></td>
                <td>
                    <?php if($current=='regulations'){ ?>
                        <?= ml('Competition.Regulation'); ?>
                    <?php }else{  ?>
                        <a href="<?= PageIndex()?>Regulations/<?= $Event['Discipline_Code'] ?>"><?= ml('Competition.Regulation'); ?></a>
                    <?php } ?>
                </td>
            </tr>    
        <?php } ?>
        <tr>
            <td><i class="fas fa-trophy"></i></td>
            <td>
                <?php if($current=='records'){ ?>
                    <?= ml('Event.Records'); ?>
                <?php }else{  ?>
                    <a href="<?= PageIndex()?>Records/all/?event=<?= $Event['Discipline_Code'] ?>"><?= ml('Event.Records'); ?></a>
                <?php } ?>
            </td>
        </tr>    
        <tr>
            <td><i class="fas fa-signal fa-rotate-90"></i></td>
            <td>
                <?php if($current=='rankings'){ ?>
                    <?= ml('Competition.Rankings'); ?>
                <?php }else{  ?>
                    <a href="<?= LinkDiscipline($Event['Discipline_Code'])?>"><?= ml('Competition.Rankings'); ?></a>
                <?php } ?>
            </td>    
        </tr>    
        <?php if($status){ ?>    
            <?= scramble_block($Event['Discipline_ID']);?>
            <?= scorecard_block($Event['Discipline_ID']);?>
        <?php } ?>
        <?php
        $exists_GenerateTraining=file_exists("Functions/GenerateTraining_{$Event['Discipline_CodeScript']}.php");
        $exists_Generate=file_exists("Functions/Generate_{$Event['Discipline_CodeScript']}.php");
        $exists_ScriptGenerate=file_exists("Script/{$Event['Discipline_CodeScript']}_generator.js");
        $exists_ScrambleImage=file_exists("Scramble/{$Event['Discipline_CodeScript']}.php");
        if($exists_ScrambleImage and ($exists_GenerateTraining or $exists_Generate or $exists_ScriptGenerate)){ ?>
        <tr>
            <td><i class="fas fa-random"></i></td>
            <td><a class="<?= $current=='training'?'select':''?>" href="<?= PageIndex()?>Event/<?= $Event['Discipline_Code'] ?>/Training"><?= ml('TrainingScrambling.Title') ?></a></td>            
        <?php } ?>
    <?php if(!$table_exists){ ?></table><?php } ?>
<?php $return = ob_get_contents();
    ob_end_clean();
    return "<nobr>$return</nobr>";
}