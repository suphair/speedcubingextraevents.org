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
            <a target="_blank"  href="<?= PageIndex() ?>Scramble/<?= $date['Event_ID']?>">
                <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/scramble.png">
                <?= ml('Function.Scramble') ?></a>
    <?php } ?>
    <?php
    $return = ob_get_contents();
    ob_end_clean();
    return "<nobr>$return</nobr>";
}