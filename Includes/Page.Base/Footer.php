<?php  $Footer_links=[
            ['Visiters','Visiters','Footer.Visiters'],
            ['Texts','Texts','Footer.Texts'],
            ['Competition.Add','Competition/Add','Footer.Competition.Add'],
            ['Event.Add','Event/Add','Footer.Event.Add'],
            ['aNews','aNews/Add','Footer.aNews.Add'],
            ['Delegate.Candidates','Delegate/Candidates','Footer.Delegate.Candidates'],
            ['Competition.Report','Reports','Footer.Reports'],
            ['Delegates.Settings','Delegates/Settings','Footer.Delegates.Settings'],
            ['MultiLanguage','MultiLanguage','Footer.MultiLanguage'],
            ['Access','Access','Footer.Access'],
            ['Logs.Authorisations','Logs/Authorisations','Footer.Logs.Authorisations'],
            ['Logs.Registrations','Logs/Registrations','Footer.Logs.Registrations'],
            ['Logs.Scrambles','Logs/Scrambles','Footer.Logs.Scrambles'],
            ['Logs.Cron','Logs/Cron','Footer.Logs.Cron'],
            ['Logs.Mail','Logs/Mail','Footer.Logs.Mail']
        ];
    $actions_grand=[]; 
    foreach($Footer_links as $link){
        if(CheckAccess($link[0])){
            ob_start(); ?>
            <nobr><a href='<?= PageIndex().$link[1] ?>'><?= ml($link[2]) ?></a></nobr>
            <?php $actions_grand[] = ob_get_contents();
            ob_clean();
        } 
    }

    if(!empty($actions_grand)){ ?>
        <div class="content"> 
            <?=implode(" &#9642; ",$actions_grand); ?>
        </div>
    <?php } ?>