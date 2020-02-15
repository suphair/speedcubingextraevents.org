<h1>Reports</h1>

<?php 
DataBaseClass::Query("Select Competition,Delegate,D.Name,C.Country,C.ID,C.WCAID from CompetitionDelegate CD"
        . " join Delegate D on D.ID=CD.Delegate left outer join Competitor C on C.WID=D.WID "
        . " where Delegate not in  (Select ID from Delegate where Status='Archive')");
$CompetitionDelegate=[];
foreach(DataBaseClass::getRows() as $row){
    $CompetitionDelegate[$row['Competition']][]=$row;
}


DataBaseClass::Query("Select Competition,Delegate,DelegateWCA, C.Name, C.ID,C.Country,C.WCAID from CompetitionReport CR"
        . " left outer join Delegate D on D.ID=CR.Delegate"
        . " left outer join Competitor C on C.WID=CR.DelegateWCA or D.WID=C.WID");
$CompetitionReport=[];
$CompetitionReportWCA=[];
foreach(DataBaseClass::getRows() as $row){
    if($row['Delegate']){
        $CompetitionReport[$row['Competition']][$row['Delegate']]=$row;
    }
    if($row['DelegateWCA']){
        $CompetitionReportWCA[$row['Competition']][]=$row;
    }
}

 DataBaseClass::Query("Select C.DelegateWCAOn,"
        . " C.Country Competition_Country, C.WCA Competition_WCA, C.ID Competition_ID, C.StartDate Competition_StartDate , C.EndDate Competition_EndDate, C.Name Competition_Name "
        . " from  Competition C "
        . " where C.StartDate<=now()"
        . " order by C.StartDate desc"); ?>

<table class="table_new">
    <thead>
        <td>Report</td>
        <td></td>
        <td>Competition</td>
        <td>Date</td>
        <td>Delegate</td>
    </thead>
    <?php foreach(DataBaseClass::getRows() as $competition){ ?>
        <?php if(!isset($CompetitionDelegate[$competition['Competition_ID']]) and !isset($CompetitionReportWCA[$competition['Competition_ID']])){ ?>
            <tr>
                <td>
                    <?php if($competition['DelegateWCAOn']){ ?>
                        <i class="fas fa-eye-slash"></i>
                    <?php }else{ ?>
                        <i class="fas fa-minus"></i>
                     <?php } ?>   
                </td>
                <td><?= ImageCountry($competition['Competition_Country'])?></td> 
                <td><a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name']?></a></td>    
                <td><?= date_range($competition['Competition_StartDate'], $competition['Competition_EndDate'])?></td>
                <td>
                    <?php if($competition['DelegateWCAOn']){ ?>
                        <i class="fas fa-hourglass-half"></i> Waiting for a WCA Delegate
                    <?php }else{ ?>
                        No one to write a report
                    <?php } ?>
                </td>
            </tr>     
        <?php }else{
            if(isset($CompetitionDelegate[$competition['Competition_ID']]))
            foreach($CompetitionDelegate[$competition['Competition_ID']] as $delegate){
                $r=false; ?>
            <tr>
                <td>
                    <?php if(isset($CompetitionReport[$competition['Competition_ID']][$delegate['Delegate']])){ ?>
                        <a href="<?= PageIndex()?>Competition/<?= $competition['Competition_WCA'] ?>/Report/<?= $delegate['WCAID'] ?>"><i class="far fa-eye"></i> View</a> 
                    <?php }else{ ?>
                        <i class="fas fa-eye-slash"></i>
                        <?php $r=true; ?>
                    <?php } ?>
                </td>
                <td><?= ImageCountry($competition['Competition_Country'])?></td>
                <td><a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name']?></a></td>    
                <td><?= date_range($competition['Competition_StartDate'], $competition['Competition_EndDate'])?></td>
                <td>
                    <?php if($r){ ?>
                        <i class="fas fa-hourglass-half"></i>
                    <?php }else{ ?>
                        <i class="far fa-check-circle"></i>
                    <?php } ?>
                    <a target="_blank" href="<?= LinkDelegate($delegate['WCAID'])?>"><?= short_Name($delegate['Name']) ?></a>
                 </td>
            </tr>     
            <?php } 
            if(isset($CompetitionReportWCA[$competition['Competition_ID']]))
            foreach($CompetitionReportWCA[$competition['Competition_ID']] as $report){ ?>
                <tr>
                    <td>
                        <a href="<?= PageIndex()?>Competition/<?= $competition['Competition_WCA'] ?>/Report/<?= $report['WCAID'] ?>"><i class="far fa-eye"></i> View</a> 
                    </td>
                    <td><?= ImageCountry($competition['Competition_Country'])?></td>
                    <td><a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name']?></a></td>    
                    <td><?= date_range($competition['Competition_StartDate'], $competition['Competition_EndDate'])?></td>
                    <td><i class="fas fa-check-circle"></i> <?= short_Name($report['Name']) ?></td>
                </<tr>
            <?php } ?>    
        <?php } ?>
    <?php } ?>
</table>