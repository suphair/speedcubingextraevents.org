<h1><img src='<?= PageIndex() ?>Image/Icons/report.png' width='30px'> <?= ml('Reports.Title')?></h1>

<?php 
DataBaseClass::Query("Select Competition,Delegate,D.Name,C.Country,C.ID,C.WCAID from CompetitionDelegate CD"
        . " join Delegate D on D.ID=CD.Delegate left outer join Competitor C on C.WID=D.WID "
        . " where Delegate not in  (Select ID from Delegate where Status='Archive')");
$CompetitionDelegate=[];
foreach(DataBaseClass::getRows() as $row){
    $CompetitionDelegate[$row['Competition']][]=$row;
}


DataBaseClass::Query("Select Competition,Delegate,DelegateWCA, C.Name, C.ID,C.Country from CompetitionReport CR"
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

<table>
    <?php foreach(DataBaseClass::getRows() as $competition){ ?>
        <?php if(!isset($CompetitionDelegate[$competition['Competition_ID']]) and !isset($CompetitionReportWCA[$competition['Competition_ID']])){ ?>
            <tr>
                <td>
                    <?= svg_red(10)?>
                </td>
                <td><?= ImageCountry($competition['Competition_Country'],20)?> <a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name']?></a></td>    
                <td><?= date_range($competition['Competition_StartDate'], $competition['Competition_EndDate'])?></td>
                <td>
                    <?php if($competition['DelegateWCAOn']){ ?>
                        <?= svg_blue(10)?> Waiting for a WCA Delegate report
                    <?php }else{ ?>
                        <?= svg_red(10)?> No one to write a report
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
                        <a href="<?= PageIndex()?>Competition/<?= $competition['Competition_WCA'] ?>/Report"><?= ml('*.Report') ?></a> 
                    <?php }else{ ?>
                        <?= svg_red(10)?>
                        <?php $r=true; ?>
                    <?php } ?>
                </td>
                <td><?= ImageCountry($competition['Competition_Country'],20)?> <a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name']?></a></td>    
                <td><?= date_range($competition['Competition_StartDate'], $competition['Competition_EndDate'])?></td>
                <td>
                    <?php if($r){ ?>
                        <?= svg_red(10)?>
                    <?php }else{ ?>
                        <?= svg_green(10)?>
                    <?php } ?>
                    <?= ImageCountry($delegate['Country'],20)?> <a href="<?= LinkDelegate($delegate['WCAID'])?>"><?= short_Name($delegate['Name']) ?></a>
                 </td>
            </tr>     
            <?php } 
            if(isset($CompetitionReportWCA[$competition['Competition_ID']]))
            foreach($CompetitionReportWCA[$competition['Competition_ID']] as $report){ ?>
                <tr>
                    <td>
                        <a href="<?= PageIndex()?>Competition/<?= $competition['Competition_WCA'] ?>/Report"><?= ml('*.Report') ?></a> 
                    </td>
                    <td><?= ImageCountry($competition['Competition_Country'],20)?> <a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name']?></a></td>    
                    <td><?= date_range($competition['Competition_StartDate'], $competition['Competition_EndDate'])?></td>
                    <td><?= svg_blue(10)?><img style="vertical-align: middle;" src="<?=PageIndex()?>Image/Icons/WCA.png" width="15px"> <?= short_Name($report['Name']) ?></td>
                </<tr>
            <?php } ?>    
        <?php } ?>
    <?php } ?>
</table>