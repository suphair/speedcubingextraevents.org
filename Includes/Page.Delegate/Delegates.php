<?php includePage('Navigator'); ?>
<div class="delegates_list" id="Delegates">   
<?php
DataBaseClass::Query("Select DATEDIFF(max(C.EndDate),now()) Latestactivity,"
        . " max(C.EndDate)  EndDate,"
        . " DATEDIFF(case when now()<max(C.EndDate) then max(C.EndDate) else now() end ,min(C.EndDate)) Period, "
        . "D.*,DelC.Avatar,DelC.Country, count(distinct Cm.ID) Count_Competitors, count(distinct C.ID) Count_Competitions"
        . "   from Delegate D left outer join Competitor DelC on "
        . " ((D.WCA_ID and D.WCA_ID=DelC.WCAID) or (D.WID and D.WID=DelC.WID)) "
        . " left outer join CompetitionDelegate CD on CD.Delegate=D.ID"
        . " left outer join Competition C on C.ID=CD.Competition"
        . " left outer join Event E on E.Competition=C.ID"
        . " left outer join Command Com on Com.Event=E.ID and Com.Decline!=1"
        . " left outer join CommandCompetitor CC on CC.Command=Com.ID"
        . " left outer join Competitor Cm on Cm.ID=CC.Competitor "
        . (!CheckAccess('Delegates.Arhive')?" where D.Status!='Archive' ":"")
        . " group by D.ID, DelC.Country,DelC.Avatar "
        . " order by case when D.Status='Archive' then 1 else 0 end,DelC.Country, "
        . " case D.Status when 'Senior' then 1  when 'Middle' then 2  when 'Junior' then 3 when 'Trainee' then 4 end  , D.Name");
$Delegate_rows=DataBaseClass::GetRows();
?>
    <h3>
<?php if(CheckAccess('Delegate.Candidates')){ ?>    
    <a href='<?= LinkDelegate("Candidates") ?>'><?= ml('Footer.Delegate.Candidates') ?></a>    
<?php }else{ ?>
    <a href="<?= PageIndex()."Delegate/Candidate" ?>"><span class='error'><?= ml('Delegate.Candidate.Title') ?></span></a>
<?php } ?>    
    </h3><br>
    <table class="Disciplines">
        <tr class="tr_title">
            <td></td>
            <td><?= ml('Judgs.Table.Name') ?></td>
            <td><?= ml('Judgs.Table.Country') ?></td><td/>
            <td align="center"><img height="15px"src="Image/Icons/competitions.png"></td>
            <?php if(CheckAccess('Delegates.Statistics')){ ?>
                <td><?= ml('Judgs.Table.Days') ?></td>
                <td><?= ml('Judgs.Table.CompetitionsInMonth') ?></td>
                <td><?= ml('Judgs.Table.LatestActivity') ?></td>
            <?php } ?>
           <td align="center"><img height="15px"src="Image/Icons/persons.png"></td>
            <td><?= ml('Judgs.Table.Events') ?></td>
        </tr>
        <?php foreach($Delegate_rows as $delegate){ ?>
            <tr>
                <td>
                    <?php if($delegate['Avatar']){ ?>
                        <img class="avatar" src="<?= $delegate['Avatar'] ?>">
                    <?php } ?>
                </td>
                <td>
                <a href="<?= LinkDelegate($delegate['WCA_ID'])?>"><?= Short_Name($delegate['Name']) ?></a>
                </td>
                <td><?= ImageCountry($delegate['Country'], 30)?> <?= CountryName($delegate['Country'])?></td>
                <td>
                    <span class='<?= $delegate['Status']=='Archive'?'archive':'' ?>
                          <?= $delegate['Status']=='Trainee'?'':'' ?>
                          <?= $delegate['Status']=='Senior'?'message':'' ?>'>
                    <?= ml('Delegate.'.$delegate['Status']) ?></span>
                </td>
                <td class="attempt">
                    <?= $delegate['Count_Competitions'] ?>
                </td>

                <?php if(CheckAccess('Delegates.Statistics')){ ?>
                    <td align="right" class="border-left-dotted">    
                        <?= $delegate['Period'] ?>
                    </td>
                    <td align="right">
                        <?php if ($delegate['Count_Competitions']>0 and $delegate['Period']>30){ 
                            $r= round($delegate['Count_Competitions']/$delegate['Period']*30,1); ?>
                            <span class="
                                <?= $r<=0.4?'error':''?>
                                <?= $r>=1?'message':''?>">
                                <?= $r ?>   
                            </span>   
                        <?php } ?>  
                    </td>    
                    <td align="right" class="border-right-dotted"> 
                        <span class="
                        <?= $delegate['Latestactivity']<-120?'error':''?>
                        <?= $delegate['Latestactivity']>-30?'message':''?>">
                            <?= $delegate['Latestactivity'] ?>
                        </span>
                        <span class="<?= strtotime($delegate['EndDate'])>time()?'message':'' ?>">
                        [<?= date_range($delegate['EndDate']); ?>]
                        </span>
                    </td>
                <?php } ?>

                <td class="attempt">
                    <?= $delegate['Count_Competitors'] ?>
                </td>
                <td>
                    <?php DataBaseClass::FromTable("Delegate","ID=".$delegate['ID']);
                    DataBaseClass::Join_current("CompetitionDelegate");
                    DataBaseClass::Join_current("Competition");
                    DataBaseClass::Join_current("Event");
                    DataBaseClass::Join_current("DisciplineFormat");
                    DataBaseClass::Join_current("Discipline");
                    DataBaseClass::OrderClear("Discipline", "Code");
                    DataBaseClass::Select("Distinct D.*");
                    $j=0; 
                    foreach(DataBaseClass::QueryGenerate() as $discipline){ ?>
                          <a href="<?= LinkDiscipline($discipline['Code']) ?>"><?= ImageDiscipline($discipline['CodeScript'],30,$discipline['Name']);?></a>
                          <?php $j++;
                          if(($j==6 and CheckAccess('Delegates.Statistics')) or $j==12){
                              $j=0;
                          echo "<br>";
                      }
                    } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<?= mlb('Delegate.Senior') ?>
<?= mlb('Delegate.Middle') ?>
<?= mlb('Delegate.Junior') ?>
<?= mlb('Delegate.Trainee') ?>
