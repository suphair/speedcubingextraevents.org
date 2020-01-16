<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$Competitor= GetCompetitorData();


DataBaseClass::FromTable('Competition',"ID='".$Competition['Competition_ID']."'");
DataBaseClass::Join_current('CompetitionDelegate');
DataBaseClass::Join_current( 'Delegate');
$delegates=DataBaseClass::QueryGenerate();


DataBaseClass::FromTable('Competition',"ID='".$Competition['Competition_ID']."'");
DataBaseClass::Join_current('CompetitionReport');
DataBaseClass::Join_current( 'Delegate');
$DelegateReports=DataBaseClass::QueryGenerate();
$reports=[];
foreach($DelegateReports as $row){
    $reports[$row['Delegate_ID']]=1;
} 

DataBaseClass::Query("Select C.Name Competitor_Name,C.ID Competitor_ID from CompetitionReport CR  join Competitor C on C.WID=CR.DelegateWCA where CR.Competition='".$Competition['Competition_ID']."'");
$delegatesWCA=DataBaseClass::getRows();

?>
<?php if(sizeof($delegates)){ ?>
    <div class="block_comment"> <b>Delegates SEE</b>
        <?php foreach($delegates as $delegate){
            if(isset($reports[$delegate['Delegate_ID']])){ ?>
               <?= svg_green(10); ?> 
            <?php }else{ ?>
                <?= svg_red(10); ?> 
            <?php } ?>
            <?= short_name($delegate['Delegate_Name']); ?>
        <?php } ?>
    </div>
<?php } ?>

<?php if(sizeof($delegatesWCA)){ ?>
    <div class="block_comment"> <b>Delegates WCA</b>
        <?php foreach($delegatesWCA as $delegate){ ?>
               <?= svg_blue(10); ?> 
            <?= short_name($delegate['Competitor_Name']); ?>
        <?php } ?>
    </div>
<?php } ?>


<?php

DataBaseClass::Query("Select CR.CreateTimestamp CompetitionReport_CreateTimestamp, " 
        . " C.Country Competition_Country,  CR.Report CompetitionReport_Report, C.WCA Competition_WCA, C.ID Competition_ID, C.StartDate Competition_StartDate , C.EndDate Competition_EndDate, C.Name Competition_Name, "
        . " Cm.Name Competitor_Name,Cm.WID Competitor_WID, Cm.ID Competitor_ID, Cm.Country Competitor_Country "
        . " , CR.DelegateWCA, CR.Parsedown CompetitionReport_Parsedown"
        . " from  Competition C "
        . " join CompetitionReport CR  on C.ID=CR.Competition "
        . " left outer join Delegate D on D.ID=CR.Delegate "
        . " left outer join Competitor Cm on Cm.WID=CR.DelegateWCA or Cm.WID=D.WID"
        . " where C.StartDate<=now() and C.ID=".$Competition['Competition_ID']
        . " order by Competitor_Name");
?>

<h1><img style="vertical-align: middle" width="40px" src="<?= PageIndex()?>Image/Icons/report.png"> <?= ml('Competition.Report.Title') ?>: <a href="<?= LinkCompetition($Competition['Competition_WCA'])?>"><?= $Competition['Competition_Name'] ?></a></h1>
<?php foreach(DataBaseClass::getRows() as $report){ 
    $Report[$report['Competitor_ID']]=$report['CompetitionReport_Report'];
    $Parsedown[$report['Competitor_ID']]=$report['CompetitionReport_Parsedown'];
     ?>
    <div class="form" style="width:1000px">
        <b>Report by <?= $report['Competitor_Name'] ?></b> (<?= $report['CompetitionReport_CreateTimestamp'] ?>)<br>
        <?php if(!$report['CompetitionReport_Parsedown']){?>
            <?= str_replace("\n","<br>",$report['CompetitionReport_Report']); ?>
        <?php }else{ ?>
            <?= Parsedown($report['CompetitionReport_Report']); ?>
        <?php }        
        DataBaseClass::Query("Select CRC.Comment,D.Name,CRC.CommentDelegate "
                . " from CompetitionReportComment CRC "
                . " join Delegate D on D.ID=CRC.CommentDelegate "
                . " where CRC.Competition=".$Competition['Competition_ID']." and CRC.Delegate=".$report['Competitor_WID']);
            $comment=false;
            foreach(DataBaseClass::getRows() as $row){
                if(trim($row['Comment'])){ 
                    if($row['CommentDelegate']== CashDelegate()['Delegate_ID']){ 
                        $comment= trim($row['Comment']);
                    } ?>
        <div class="block_comment"><b><?= $row['Name'] ?></b><br><?= Parsedown($row['Comment']) ?></div>
            <?php }
            }
        if(CheckAccess('Competition.Report.Comment',$Competition['Competition_ID'])){  ?> 
            <div class="block_comment">
                <form method="POST" action="<?= PageAction('Competition.Edit.Report.Comment')?>">
                    Сomment by <b><?= $Competitor->name ?></b><br>
                    <input name="Competition" type="hidden" value="<?= $report['Competition_ID'] ?>" />        
                    <input name="Delegate" type="hidden" value="<?= $report['Competitor_WID'] ?>" />        
                    <textarea name="Comment"><?= $comment ?></textarea>
                    <input type="submit" value="<?= ml('*.Save',false)?>">
                </form>
            </div>    
        <?php } ?>
    </div>
<?php } ?>


<?php $Instruction=GetBlockText("Report"); 
if(CheckAccess('Competition.Report.Create',$Competition['Competition_ID'])){ ?> 
    <br><b>Enter report by <?= $Competitor->name ?></b><br>
    <div class="block_comment">
    <b>Instruction</b><br>
    <?= Parsedown($Instruction); ?>
    </div>
    <form method="POST" action="<?= PageAction('Competition.Edit.Report')?>">
        <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
        <textarea name="Report" style="height: 400px;width: 1000px"><?= 
            isset($Report[$Competitor->local_id])?$Report[$Competitor->local_id]:'' 
        ?></textarea><br>
        Using <a target="_blank" href="https://parsedown.org">Markdown</a> <input name="Parsedown" type="checkbox" <?= (isset($Parsedown[$Competitor->local_id]) and $Parsedown[$Competitor->local_id])?'checked':'' ?>/>
        <input type="submit" name="submit" value="Save report"> 
    </form> 
<?php } ?>