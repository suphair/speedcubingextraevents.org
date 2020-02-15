<?php  $Footer_links=[
            ['Visiters','Visiters','Visiters'],
            ['Texts','Texts','Texts'],
            ['Competition.Add','Competition/Add','Add Competition'],
            ['Event.Add','Event/Add','Add Event'],
            ['aNews','aNews/Add','Add aNews'],
            ['Delegate.Candidates','Delegate/Candidates','Applications to Delegate'],
            ['Competition.Report','Reports','Reports'],
            ['Delegates.Settings','Delegates/Settings','Delegate Changes'],
            ['MultiLanguage','MultiLanguage','Multi language'],
            ['Access','Access','Access'],
            ['Logs.Authorisations','Logs/Authorisations','Logs authorisations'],
            ['Logs.Registrations','Logs/Registrations','Logs registrations'],
            ['Logs.Scrambles','Logs/Scrambles','Logs scrambles'],
            ['Logs.Cron','Logs/Cron','Logs cron'],
            ['Logs.Mail','Logs/Mail','Logs mail']
        ];
    $actions_grand=[]; 
    foreach($Footer_links as $link){
        if(CheckAccess($link[0])){
            ob_start(); ?>
            <nobr><a href='<?= PageIndex().$link[1] ?>'><?= $link[2] ?></a></nobr>
            <?php $actions_grand[] = ob_get_contents();
            ob_clean();
        } 
    }

    if(!empty($actions_grand)){ ?>
        <div class="content"> 
            <b>Delegate's links:</b> 
            <?=implode(" &#9642; ",$actions_grand); ?>
        </div>
    <?php } ?>