<?php 
$Competitor=GetCompetitorData();
$Delegate=CashDelegate(); 

if($Competitor){ ?>
    <?= Short_Name($Competitor->name) ?> 
        <a href="<?= PageIndex() ?>Competitor/<?= $Competitor->local_id ?>"><img src='<?= PageIndex()?>Image/Icons/noavatar.png' width='12px'> <?= ml('Competitor.MyResults') ?></a>
        <a href="<?= PageIndex() ?>Competitions/My"><img src='<?= PageIndex()?>Image/Icons/mycompetitions.png' width='12px'> <?= ml('Competitor.MyCompetitions') ?></a>   
        <?php if($Delegate){ ?>
            <nobr>&#9642; <a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']); ?>">
                <?= ml('Competitor.Login.Delegate') ?>
            </a></nobr>
        <?php } ?> 

        <?php if(CheckAccess('Delegate.Candidate.Vote')){
            DataBaseClass::Query("Select * from RequestCandidate RC "
                        . " left outer join RequestCandidateVote RCV on RCV.Competitor=RC.Competitor and RCV.Delegate=".$Delegate['Delegate_ID']
                        . " where RC.Status=0 and coalesce(RCV.Status,-2)=-2"); ?>
            <?php if(sizeof(DataBaseClass::getRows())){ ?>
                <nobr>&#9642; <a href='<?= LinkDelegate("Candidates") ?>'><?= ml('Competitor.Delegate.Candidates') ?></a> <span class="badge"><?= sizeof(DataBaseClass::getRows());?></span></nobr>
            <?php } ?> 
        <?php } ?> 
                
        <?php if(CheckAccess('Event.Settings')){
            DataBaseClass::Query("Select * from Discipline D"
                         . " Left outer join Regulation R on D.ID=R.Event"
                         . " where D.Status='Active'  and R.ID is null"); ?>
            <?php if(sizeof(DataBaseClass::getRows())){ ?>
                <nobr>&#9642; <a href='<?= PageIndex() ?>Regulations'><?= ml('Competitor.Delegate.Regulations') ?></a> <span class="badge"><?= sizeof(DataBaseClass::getRows());?></span></nobr>
            <?php } ?> 
        <?php } ?> 
                
<?php }else{ ?>
    <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?> 
    <a href="<?= GetUrlWCA(); ?>"><img src='<?= PageIndex()?>Image/Icons/signin.png' width='12px'> <?= ml('Competitor.SignIn')?></a>
<?php } ?>
<form class='form_inline' method="POST" action="<?=PageAction('Language.Set')?> "> 
    <?php if($Competitor){ ?>
        <a href="<?= PageIndex() ?>Actions/Competitor.Logout"><img src='<?= PageIndex()?>Image/Icons/signout.png' width='12px'> <font color="red"><?= ml('Competitor.SignOut')?></font></a>&nbsp;
    <?php } ?>
    <?php $Language=$_SESSION['language_select']; ?>    
    <?= ImageCountry($Language,20); ?>
    <select style="width:85px;" onchange="form.submit()" name='language'>
        <?php foreach(getLanguages() as $language){ ?>
        <option <?= $Language==$language?'selected':'' ?> value="<?= $language ?>"><?= CountryName($language,true) ?></option>
        <?php } ?>
    </select>
</form>    
    
    <?= mlb('Competitor.SignIn')?>