   
<?php
DataBaseClass::Query("Select DATEDIFF(max(C.EndDate),now()) Latestactivity,"
        . " max(C.EndDate)  EndDate,"
        . " DATEDIFF(case when now()<max(C.EndDate) then max(C.EndDate) else now() end ,min(C.EndDate)) Period, "
        . "D.*,DelC.Country, count(distinct Cm.ID) Count_Competitors, count(distinct C.ID) Count_Competitions"
        . "   from Delegate D left outer join Competitor DelC on "
        . " ((D.WCA_ID and D.WCA_ID=DelC.WCAID) or (D.WID and D.WID=DelC.WID)) "
        . " left outer join CompetitionDelegate CD on CD.Delegate=D.ID"
        . " left outer join Competition C on C.ID=CD.Competition"
        . " left outer join Event E on E.Competition=C.ID"
        . " left outer join Command Com on Com.Event=E.ID and Com.Decline!=1"
        . " left outer join CommandCompetitor CC on CC.Command=Com.ID"
        . " left outer join Competitor Cm on Cm.ID=CC.Competitor "
        . (!CheckAccess('Delegates.Arhive')?" where D.Status!='Archive' ":"")
        . " group by D.ID, DelC.Country "
        . " order by case when D.Status='Archive' then 1 else 0 end, DelC.Country, "
        . " case D.Status when 'Senior' then 1  when 'Middle' then 2  when 'Junior' then 3 when 'Trainee' then 4 end  , D.Name");
$Delegate_rows=DataBaseClass::GetRows();
?>  
    <h1><?= ml('Navigator.Delegates') ?></h1>
<h2>
<?php if(CheckAccess('Delegate.Candidates')){ ?>    
    <a href='<?= LinkDelegate("Candidates") ?>'><?= ml('Footer.Delegate.Candidates') ?></a>    
<?php }else{ ?>
    <a href="<?= PageIndex()."Delegate/Candidate" ?>"><?= ml('Delegate.Candidate.Title') ?></a>
<?php } ?>    
</h2>  
    <table class="table_new">
        <thead>
        <tr>
            <td/>
            <td/>
            <td></td>
            <td class="table_new_center"><?= ml('Delagates.Competitions') ?></td>
            <td class="table_new_center"><?= ml('Delagates.Persons') ?></td>
            <?php if(CheckAccess('Delegates.Statistics')){ ?>
                <td class="table_new_right">Days</td>
                <td class="table_new_right">Comps per month</td>
                <td class="table_new_right">Latest activity</td>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php 
            $archive=false;
            $country=false;
          foreach($Delegate_rows as $delegate){
            if(!$archive and $delegate['Status']=='Archive'){ ?>
                <tr><td>&nbsp;</td><td/><td/><td/><td/></tr>
            <?php } ?>
            <?php if($delegate['Status']=='Archive'){
                $archive=true;
            } ?>  
            <?php if(!$archive and $delegate['Country']!=$country){ 
                $country=$delegate['Country']; ?>
                <tr>
                    <td>
                        <?= ImageCountry($delegate['Country'], 20)?>
                    </td>
                    <td>
                        <b><?= CountryName($delegate['Country'])?></b>
                    </td>
                    <td/><td/><td/>
                    <?php if(CheckAccess('Delegates.Statistics')){ ?>
                    <td/><td/><td/>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    <?php if($archive){ ?>
                        <?= ImageCountry($delegate['Country'], 20)?>
                    <?php } ?>
                </td>
                <td> 
                    <a href="<?= LinkDelegate($delegate['WCA_ID'])?>"><?= Short_Name($delegate['Name']) ?></a>
                </td>
                <td>
                    <?php if($delegate['Status']=='Archive'){ ?>
                        <i class="fas fa-ban"></i>
                    <?php } ?>
                    <?php if($delegate['Status']=='Senior'){ ?>
                        <i class="fas fa-user-tie"></i>
                    <?php } ?>
                    <?= ml('Delegate.'.$delegate['Status']) ?>
                </td>
                <td class="table_new_center">
                    <?= $delegate['Count_Competitions'] ?>
                </td>
                <td class="table_new_center">
                    <?= $delegate['Count_Competitors'] ?>
                </td>
                <?php if(CheckAccess('Delegates.Statistics')){ ?>
                    <td class="table_new_right">    
                        <?= $delegate['Period'] ?>
                    </td>
                    <td class="table_new_right">
                        <?php if ($delegate['Count_Competitions']>0 and $delegate['Period']>30){ 
                            $r= round($delegate['Count_Competitions']/$delegate['Period']*30,1); ?>
                            <?= $r ?>   
                        <?php }else{ ?>
                            -
                        <?php } ?>
                    </td>    
                    <td class="table_new_right"> 
                        <?= date_range($delegate['EndDate']); ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
    </table>
<?= mlb('Delegate.Senior') ?>
<?= mlb('Delegate.Middle') ?>
<?= mlb('Delegate.Junior') ?>
<?= mlb('Delegate.Trainee') ?>
