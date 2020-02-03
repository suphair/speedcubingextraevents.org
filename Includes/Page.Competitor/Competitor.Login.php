<?php 
$Competitor=GetCompetitorData();
$Delegate=CashDelegate(); 

if($Competitor){ ?>
    <?= svg_green(12) ?>
    <?= Short_Name($Competitor->name) ?> <a href="<?= PageIndex() ?>Actions/Competitor.Logout"><font color="red"><?= ml('Competitor.SignOut')?></font></a>
        <a href="<?= PageIndex() ?>Competitor/<?= $Competitor->local_id ?>"><img src='<?= PageIndex()?>Image/Icons/noavatar.png' width='10px'> <?= ml('Competitor.MyResults') ?></a>
        <a href="<?= PageIndex() ?>Competitions/My"><img src='<?= PageIndex()?>Image/Icons/mycompetitions.png' width='10px'> <?= ml('Competitor.MyCompetitions') ?></a>   
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
    <?= svg_red(12) ?>
    <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?> 
    <a href="<?= GetUrlWCA(); ?>"><?= ml('Competitor.SignIn')?></a>
<?php } ?>
<form class='form_inline' method="POST" action="<?=PageAction('Language.Set')?> "> 
    <?php $Language=$_SESSION['language_select']; ?>    
    <?= ImageCountry($Language,20); ?>
    <select style="width:85px;" onchange="form.submit()" name='language'>
        <?php foreach(getLanguages() as $language){ ?>
        <option <?= $Language==$language?'selected':'' ?> value="<?= $language ?>"><?= CountryName($language,true) ?></option>
        <?php } ?>
    </select>
</form>    
    
    <?= mlb('Competitor.SignIn')?>