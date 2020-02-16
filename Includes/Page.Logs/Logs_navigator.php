<?php
$Links=[
    ['Logs.Authorisations','Logs/Authorisations','Logs Authorisations'],
    ['Logs.Registrations','Logs/Registrations','Logs Registrations'],
    ['Logs.Scrambles','Logs/Scrambles','Logs Scrambles'],
    ['Logs.Cron','Logs/Cron','Logs Cron'],
    ['Logs.Mail','Logs/Mail','Logs Mail']
];
    $actions_grand=[]; 
    foreach($Links as $link){
        if(CheckAccess($link[0])){
            ob_start(); ?>
            <nobr><a href='<?= PageIndex().$link[1] ?>'><?= $link[2] ?></a></nobr>
            <?php $actions_grand[] = ob_get_contents();
            ob_clean();
        } 
    }

    if(!empty($actions_grand)){ ?>
            <?=implode(" &#9642; ",$actions_grand); ?>
    <?php } ?>
<hr>