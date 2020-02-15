<?php
function scorecard_block($ID){
        DataBaseClass::FromTable('Event');
        DataBaseClass::Join_current('DisciplineFormat');
        DataBaseClass::Where_current('Discipline='.$ID);
        DataBaseClass::Where('Event','Competition=129');
        $date=DataBaseClass::QueryGenerate(false); 
        if(isset($date['Event_ID'])){ 
            ob_start(); ?>
            <tr>
                <td><i class="fas fa-print"></i></td>
                <td><a target="_blank"  href="<?= PageAction('CompetitonEvent.ScoreCards')?>/<?= $date['Event_ID'] ?>"><?= ml('Function.ScoreCard') ?></a></td>
            </tr>                
            <?php
            $return = ob_get_contents();
            ob_end_clean();
            return $return;
        }
    return "";    
}