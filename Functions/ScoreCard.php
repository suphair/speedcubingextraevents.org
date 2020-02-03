<?php
function scorecard_block($ID){
        DataBaseClass::FromTable('Event');
        DataBaseClass::Join_current('DisciplineFormat');
        DataBaseClass::Where_current('Discipline='.$ID);
        DataBaseClass::Where('Event','Competition=129');
        $date=DataBaseClass::QueryGenerate(false); 
        if(isset($date['Event_ID'])){ 
            ob_start(); ?>
            <a target="_blank"  href="<?= PageAction('CompetitonEvent.ScoreCards')?>/<?= $date['Event_ID'] ?>">        
                <img style="vertical-align: middle" width="15px"  src="<?= PageIndex()?>Image/Icons/print.png">
                <?= ml('Function.ScoreCard') ?>
            </a>
            <?php
            $return = ob_get_contents();
            ob_end_clean();
            return "<nobr>$return</nobr>";
        }
    return "";    
}