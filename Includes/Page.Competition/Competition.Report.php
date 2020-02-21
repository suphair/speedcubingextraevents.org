<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$Competitor= getCompetitor();
$request=getRequest();

DataBaseClass::FromTable('Competition',"ID='".$Competition['Competition_ID']."'");
DataBaseClass::Join_current('CompetitionDelegate');
DataBaseClass::Join_current( 'Delegate');
DataBaseClass::OrderClear('Delegate', 'Name');
$delegates=DataBaseClass::QueryGenerate();


DataBaseClass::FromTable('Competition',"ID='".$Competition['Competition_ID']."'");
DataBaseClass::Join_current('CompetitionReport');
DataBaseClass::Join_current( 'Delegate');
$DelegateReports=DataBaseClass::QueryGenerate();
$reports=[];
foreach($DelegateReports as $row){
    $reports[$row['Delegate_ID']]=1;
} 

DataBaseClass::Query("Select C.WCAID Delegate_WCA_ID, C.Name Competitor_Name,C.ID Competitor_ID from CompetitionReport CR  join Competitor C on C.WID=CR.DelegateWCA where CR.Competition='".$Competition['Competition_ID']."'");
$delegatesWCA=DataBaseClass::getRows();
$WCAID=false;
if(isset($request[3])){
    $WCAID= strtoupper($request[3]);
}

$delegateSelected=false;
$currentDelegate=$Competitor->wca_id;

foreach(array_merge($delegatesWCA,$delegates) as $delegate){
  if($WCAID==$delegate['Delegate_WCA_ID']){
      $delegateSelected=$WCAID;
  }   
}
if(!$delegateSelected){
    foreach(array_merge($delegatesWCA,$delegates) as $delegate){
        if($currentDelegate==$delegate['Delegate_WCA_ID']){
            $delegateSelected=$currentDelegate;
        }   
    }   
}

if(!$delegateSelected){
    $delegateSelected=array_merge($delegatesWCA,$delegates)[0]['Delegate_WCA_ID'];
}  
?>

<h1><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>"><?= $Competition['Competition_Name'] ?></a> / Reports</h1>
    
<?php 
DataBaseClass::Query("Select CR.CreateTimestamp CompetitionReport_CreateTimestamp, " 
        . " C.Country Competition_Country,  CR.Report CompetitionReport_Report, C.WCA Competition_WCA, C.ID Competition_ID, C.StartDate Competition_StartDate , C.EndDate Competition_EndDate, C.Name Competition_Name, "
        . " Cm.Name Competitor_Name,Cm.WID Competitor_WID, Cm.ID Competitor_ID, Cm.Country Competitor_Country "
        . " , CR.DelegateWCA, CR.Parsedown CompetitionReport_Parsedown"
        . " from  Competition C "
        . " join CompetitionReport CR  on C.ID=CR.Competition "
        . " left outer join Delegate D on D.ID=CR.Delegate "
        . " left outer join Competitor Cm on Cm.WID=CR.DelegateWCA or Cm.WID=D.WID"
        . " where C.StartDate<=now() and C.ID=".$Competition['Competition_ID']." and Cm.WCAID='".$delegateSelected."'"
        . " order by Competitor_Name");
$report=DataBaseClass::getRow(); ?>
<table class="table_info">
    <?php if(sizeof($delegates)){ ?>
    <tr>
        <td>Delegates SEE</td>
        <td/>
    </tr>    
    <?php foreach($delegates as $delegate){ ?>
    <tr>
        <?php if(isset($reports[$delegate['Delegate_ID']])){ ?>
        <td><i class="far fa-check-circle"></i></td>
        <td>
            <a class="<?= $delegateSelected==$delegate['Delegate_WCA_ID']?'list_select':''?>" href="<?= PageIndex() ?>Competition/<?= $Competition['Competition_WCA'] ?>/Report/<?= $delegate['Delegate_WCA_ID'] ?>"><?= short_name($delegate['Delegate_Name']); ?></a>
        </td>  
     <?php }else{ ?>
        <td><i class="fas fa-hourglass-half"></i></td>
        <td>
            <?= short_name($delegate['Delegate_Name']); ?>
        </td>  
     <?php } ?>
    </tr>    
    <?php } ?>

    <?php if(sizeof($delegatesWCA)){ ?>
    <tr>
        <td>Delegates WCA</td>
        <td/>
    </tr>  
    <?php foreach($delegatesWCA as $delegate){ ?>
    <tr>
        <td><i class="fas fa-check-circle"></i></td>
        <td>
            <a class="<?= $delegateSelected==$delegate['Delegate_WCA_ID']?'list_select':''?>" href="<?= PageIndex() ?>Competition/<?= $Competition['Competition_WCA'] ?>/Report/<?= $delegate['Delegate_WCA_ID'] ?>"><?= short_name($delegate['Competitor_Name']); ?></a>
        </td>
    </tr>        
    <?php } ?>
    </div>
    <?php } ?>
    
    
<?php } ?>
</table>
<?php 
if(isset($report['Competitor_ID'])){
$Report[$report['Competitor_ID']]=$report['CompetitionReport_Report'];
    $Parsedown[$report['Competitor_ID']]=$report['CompetitionReport_Parsedown']; ?>
        <h3>Report by <?= $report['Competitor_Name'] ?> <?= date_range(date('d-m-Y',strtotime($report['CompetitionReport_CreateTimestamp']))); ?></h3>
        <?php if(!$report['CompetitionReport_Parsedown']){?>
            <?= str_replace("\n","<br>",$report['CompetitionReport_Report']); ?>
        <?php }else{ ?>
            <?= Parsedown($report['CompetitionReport_Report']); ?>
        <?php }        
        DataBaseClass::Query("Select CRC.Comment,D.Name,CRC.CommentDelegate "
                . " from CompetitionReportComment CRC "
                . " join Delegate D on D.ID=CRC.CommentDelegate "
                . " where CRC.Competition=".$Competition['Competition_ID']." and CRC.Delegate='".$report['Competitor_WID']."'"
                . " and trim(CRC.Comment)<>'' ");
            
        $comment=false;
        $comments=DataBaseClass::getRows();
        if(sizeof($comments)){ ?>
            <h3>Comment by SEE Senior Delegates</h3>
            <table class="table_info">
            <?php foreach($comments as $row){ 
                    if($row['CommentDelegate']== getDelegate()['Delegate_ID']){ 
                        $comment= trim($row['Comment']);
                    } ?>
                <tr>
                    <td><?= $row['Name'] ?></td>
                    <td><?= Parsedown($row['Comment']) ?></td>
                </tr>    
            <?php } ?>
            </table>
        <?php }
        if(CheckAccess('Competition.Report.Comment',$Competition['Competition_ID'])){  ?> 
            <h3>Add comment</h3>
            <table class="table_info">
                <tr>
                    <td><?= $Competitor->name ?></td>
                    <td>
                <form method="POST" action="<?= PageAction('Competition.Edit.Report.Comment')?>">
                    <input name="Competition" type="hidden" value="<?= $report['Competition_ID'] ?>" />        
                    <input name="Delegate" type="hidden" value="<?= $report['Competitor_WID'] ?>" />        
                    <textarea name="Comment"><?= $comment ?></textarea><br>
                    <button><i class="fas fa-comment-medical"></i> Add comment</button>
                </form>
                   </td>
                </tr>
            </table>    
        <?php } ?>
<?php }
if(CheckAccess('Competition.Report.Create',$Competition['Competition_ID']) and $Competitor->wca_id==$delegateSelected){ ?> 
<h3>Add report</h3>
<table class="table_info">
    <tr>
        <td>Instruction</td>
        <td><?= Parsedown(getBlockText("Report")); ?></td>
    </tr>
</table>
<br>
    <form method="POST" action="<?= PageAction('Competition.Edit.Report')?>">
        <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
        <textarea name="Report" style="height: 400px;width: 1000px"><?= 
            isset($Report[$Competitor->local_id])?$Report[$Competitor->local_id]:'' 
        ?></textarea><br>
        Using <a target="_blank" href="https://parsedown.org">Markdown</a> <input name="Parsedown" type="checkbox" <?= (isset($Parsedown[$Competitor->local_id]) and $Parsedown[$Competitor->local_id])?'checked':'' ?>/>
        <button><i class="far fa-file-alt"></i> Public report</button>
    </form> 
<?php } ?>