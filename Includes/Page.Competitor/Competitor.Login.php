<div style="display: none" class="competitor-panel">
<?php 
$Competitor=getCompetitor();
$Delegate=getDelegate(); 

if($Competitor){ ?>
    <a href="<?= PageIndex() ?>Competitor/<?= $Competitor->local_id ?>"><i class="fas fa-user"></i> <?= ml('Competitor.MyResults') ?></a>&nbsp;&nbsp;&nbsp;
    <a href="<?= PageIndex() ?>Competitions/My"><i class="fas fa-cube"></i> <?= ml('Competitor.MyCompetitions') ?></a>&nbsp;&nbsp;&nbsp;
    <?php if($Delegate){ ?>
        <nobr><a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']); ?>"><i class="fas fa-user-tie"></i> 
            Delegate page
        </a></nobr>&nbsp;&nbsp;&nbsp;
    <?php } 
    if(CheckAccess('Delegate.Candidate.Vote')){
        DataBaseClass::Query("Select * from RequestCandidate RC "
                    . " left outer join RequestCandidateVote RCV on RCV.Competitor=RC.Competitor and RCV.Delegate=".$Delegate['Delegate_ID']
                    . " where RC.Status=0 and coalesce(RCV.Status,-2)=-2"); ?>
        <?php if(sizeof(DataBaseClass::getRows())){ ?>
            <nobr><a href='<?= LinkDelegate("Candidates") ?>'><i class="fas fa-baby"></i> <?= sizeof(DataBaseClass::getRows());?> New candidates</a></nobr>&nbsp;&nbsp;&nbsp;
        <?php }
    } 
} 
$delegateLinks=[
            ['Competition.Report','Reports','Reports','<i class="far fa-file-alt"></i>'],
            ['Visitors','Visitors','Visitors','<i class="fas fa-user-plus"></i>'],
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
    $links=[]; 
    foreach($delegateLinks as $link){
        if(CheckAccess($link[0])){
            ob_start(); ?>
            <nobr><?= $link[3]?> <a href='<?= PageIndex().$link[1] ?>'><?= $link[2] ?></a>&nbsp;&nbsp;&nbsp;</nobr>
            <?php $links[] = ob_get_contents();
            ob_clean();
        } 
    }

    if(!empty($links)){ ?>
        <i class="fas fa-angle-double-right"></i>&nbsp;&nbsp;&nbsp;
        <?= implode("",$links); ?>
    <?php } ?>          
</div>     