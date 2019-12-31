<?php
function scorecard_block($ID){
    ob_start(); 
        DataBaseClass::FromTable('Event');
        DataBaseClass::Join_current('DisciplineFormat');
        DataBaseClass::Where_current('Discipline='.$ID);
        DataBaseClass::Where('Event','Competition=129');
        $date=DataBaseClass::QueryGenerate(false); ?>
        <a target="_blank"  href="<?= PageAction('CompetitonEvent.ScoreCards')?>/<?= $date['Event_ID'] ?>">        
            <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/print.png">
            <?= ml('Function.ScoreCard') ?>
        </a>
    <?php
    $return = ob_get_contents();
    ob_end_clean();
    return "<nobr>$return</nobr>";
}