<?php
$Links=[
    ['Logs.Authorisations','Logs/Authorisations','Footer.Logs.Authorisations'],
    ['Logs.Registrations','Logs/Registrations','Footer.Logs.Registrations'],
    ['Logs.Scrambles','Logs/Scrambles','Footer.Logs.Scrambles'],
    ['Logs.Cron','Logs/Cron','Footer.Logs.Cron'],
    ['Logs.Mail','Logs/Mail','Footer.Logs.Mail']
];
    $actions_grand=[]; 
    foreach($Links as $link){
        if(CheckAccess($link[0])){
            ob_start(); ?>
            <nobr><a href='<?= PageIndex().$link[1] ?>'><?= ml($link[2]) ?></a></nobr>
            <?php $actions_grand[] = ob_get_contents();
            ob_clean();
        } 
    }

    if(!empty($actions_grand)){ ?>
            <?=implode(" &#9642; ",$actions_grand); ?>
    <?php } ?>
<hr>