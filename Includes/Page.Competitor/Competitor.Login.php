&nbsp;<?php 
$Competitor=GetCompetitorData();
$Delegate=CashDelegate(); 

if($Competitor){ ?>
    <?= Short_Name($Competitor->name) ?> 
        <a href="<?= PageIndex() ?>Competitor/<?= $Competitor->local_id ?>"><i class="fas fa-user"></i> <?= ml('Competitor.MyResults') ?></a>
        <a href="<?= PageIndex() ?>Competitions/My"><i class="fas fa-cube"></i> <?= ml('Competitor.MyCompetitions') ?></a>   
        <?php if($Delegate){ ?>
            <nobr><a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']); ?>"><i class="fas fa-user-tie"></i> 
                Delegate page
            </a></nobr>
        <?php } ?> 

        <?php if(CheckAccess('Delegate.Candidate.Vote')){
            DataBaseClass::Query("Select * from RequestCandidate RC "
                        . " left outer join RequestCandidateVote RCV on RCV.Competitor=RC.Competitor and RCV.Delegate=".$Delegate['Delegate_ID']
                        . " where RC.Status=0 and coalesce(RCV.Status,-2)=-2"); ?>
            <?php if(sizeof(DataBaseClass::getRows())){ ?>
                <nobr><a href='<?= LinkDelegate("Candidates") ?>'><i class="fas fa-baby"></i> <?= sizeof(DataBaseClass::getRows());?> New candidates</a></nobr>
            <?php } ?> 
        <?php } ?> 
                
        <?php if(CheckAccess('Event.Settings')){
            DataBaseClass::Query("Select * from Discipline D"
                         . " Left outer join Regulation R on D.ID=R.Event"
                         . " where D.Status='Active'  and R.ID is null"); ?>
            <?php if(sizeof(DataBaseClass::getRows())){ ?>
                <nobr><a href='<?= PageIndex() ?>Regulations'><i class="fas fa-book"></i> <?= sizeof(DataBaseClass::getRows());?> Events without regulations </a></nobr>
            <?php } ?> 
        <?php } ?> 
<?php } ?>
<form class='form_inline' method="POST" action="<?=PageAction('Language.Set')?> "> 
    <?php if($Competitor){ ?>
        <a href="<?= PageIndex() ?>Actions/Competitor.Logout"><i class="fas fa-sign-out-alt"></i> <?= ml('Competitor.SignOut')?></a>&nbsp;
    <?php }else{ ?>
        <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?> 
        <a href="<?= GetUrlWCA(); ?>"><i class="fas fa-sign-in-alt"></i> <?= ml('Competitor.SignIn')?></a>&nbsp;
    <?php } ?>   
    <?php $Language=$_SESSION['language_select']; ?>    
    <?= ImageCountry($Language,20); ?>
    <select style="width:85px;" onchange="form.submit()" name='language'>
        <?php foreach(getLanguages() as $language){ ?>
        <option <?= $Language==$language?'selected':'' ?> value="<?= $language ?>"><?= CountryName($language,true) ?></option>
        <?php } ?>
    </select>
</form>    