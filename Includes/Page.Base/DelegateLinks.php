<?php  $Footer_links=[
            ['Competition.Report','Reports','Reports','<i class="far fa-file-alt"></i>'],
            ['Visiters','Visiters','Visiters','<i class="fas fa-user-plus"></i>'],
            ['Texts','Texts','Texts','<i class="fas fa-file-alt"></i>'],
            ['Delegates.Settings','Delegates/Settings','Delegate Changes','<i class="fas fa-user-cog"></i>'],
            ['MultiLanguage','MultiLanguage','Multi language','<i class="fas fa-language"></i>'],
            ['Access','Access','Access','<i class="fas fa-id-badge"></i>'],
            ['Logs.Authorisations','Logs/Authorisations','Logs authorisations','<i class="fas fa-list"></i>'],
            ['Logs.Registrations','Logs/Registrations','Logs registrations','<i class="fas fa-list"></i>'],
            ['Logs.Scrambles','Logs/Scrambles','Logs scrambles','<i class="fas fa-list"></i>'],
            ['Logs.Cron','Logs/Cron','Logs cron','<i class="fas fa-list"></i>'],
            ['Logs.Mail','Logs/Mail','Logs mail','<i class="fas fa-list"></i>']
        ];
    $actions_grand=[]; 
    foreach($Footer_links as $link){
        if(CheckAccess($link[0])){
            ob_start(); ?>
            <nobr>
                <?php if(isset($link[3])){ ?>
                    <?= $link[3]?>
                <?php }else{ ?>
                    &#9642;
                <?php } ?>
                <a href='<?= PageIndex().$link[1] ?>'><?= $link[2] ?></a>&nbsp;&nbsp;&nbsp;</nobr>
            <?php $actions_grand[] = ob_get_contents();
            ob_clean();
        } 
    }

    if(!empty($actions_grand)){ ?>
        <i class="fas fa-angle-double-right"></i>&nbsp;&nbsp;&nbsp;
        <?=implode("",$actions_grand); ?>
    <?php } ?>