<?php
function scramble_block($ID){
    ob_start(); 
        DataBaseClass::FromTable('Event');
        DataBaseClass::Join_current('DisciplineFormat');
        DataBaseClass::Where_current('Discipline='.$ID);
        DataBaseClass::Where('Event','Competition=129');
        $date=DataBaseClass::QueryGenerate(false);
        DataBaseClass::Join('Event','Scramble');
        $scramble=DataBaseClass::QueryGenerate(false);
                   
    $file="Image/Scramble/".$date['Event_ScrambleSalt'].".pdf";
        if ($date['Event_ScrambleSalt'] and file_exists($file)){ ?>
            <tr>
                <td><i class="fas fa-print"></i></td>
                <td><a target="_blank"  href="<?= PageIndex() ?>/Scramble/<?= $date['Event_ID']?>"> <?= ml('Function.Scramble') ?></a></td>
            </tr>    
    <?php } ?>
    <?php
    $return = ob_get_contents();
    ob_end_clean();
    return $return;
}

function GetLinkScrambes($event){
    if(file_exists('Scramble/'.$event['Discipline_CodeScript'].'.php')){
            if(file_exists('Functions/Generate_'.$event['Discipline_CodeScript'].'.php')){
                return PageAction('CompetitionEvent.Scramble.Generate');
            }else{
                if(!$event['Discipline_GlueScrambles'] and file_exists('Includes/CompetitionEvent/Action/CompetitionEvent.Scramble.'.$event['Discipline_CodeScript'].'.php') ){
                    return PageAction('CompetitionEvent.Scramble.'.$event['Discipline_CodeScript']);
                }else{
                    return PageAction('CompetitionEvent.Scramble.Page');
                }
            }
        }else{
            if($event['Discipline_GlueScrambles'] and $event['Discipline_TNoodles']){
                return PageAction('CompetititionEvent.GlueScrambles.TNoodles');
            }
            if($event['Discipline_GlueScrambles'] and $event['Discipline_TNoodle']){
                return PageAction('CompetititionEvent.GlueScrambles.TNoodle');
            }
         }
    return false;            
}