<div style="display: none" class="competitor-panel"><?php 
$Competitor=GetCompetitorData();
$Delegate=CashDelegate(); 

if($Competitor){ ?>
    <a href="<?= PageIndex() ?>Competitor/<?= $Competitor->local_id ?>"><i class="fas fa-user"></i> <?= ml('Competitor.MyResults') ?></a>&nbsp;&nbsp;&nbsp;
    <a href="<?= PageIndex() ?>Competitions/My"><i class="fas fa-cube"></i> <?= ml('Competitor.MyCompetitions') ?></a>&nbsp;&nbsp;&nbsp;
    <?php if($Delegate){ ?>
        <nobr><a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']); ?>"><i class="fas fa-user-tie"></i> 
            Delegate page
        </a></nobr>&nbsp;&nbsp;&nbsp;
    <?php } ?> 

    <?php if(CheckAccess('Delegate.Candidate.Vote')){
        DataBaseClass::Query("Select * from RequestCandidate RC "
                    . " left outer join RequestCandidateVote RCV on RCV.Competitor=RC.Competitor and RCV.Delegate=".$Delegate['Delegate_ID']
                    . " where RC.Status=0 and coalesce(RCV.Status,-2)=-2"); ?>
        <?php if(sizeof(DataBaseClass::getRows())){ ?>
            <nobr><a href='<?= LinkDelegate("Candidates") ?>'><i class="fas fa-baby"></i> <?= sizeof(DataBaseClass::getRows());?> New candidates</a></nobr>&nbsp;&nbsp;&nbsp;
        <?php } ?> 
    <?php } ?> 

<?php } ?>
            
<?php IncludePage("DelegateLinks"); ?>
</div>     