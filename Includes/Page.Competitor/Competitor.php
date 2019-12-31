<?php includePage('Navigator'); ?>
<?php $competitor= ObjectClass::getObject('PageCompetitor'); 

 DataBaseClass::Query("Select Ct.Name,Ct.Code from Country C"
         . " join Continent Ct on Ct.Code=C.Continent where C.ISO2='".$competitor['Competitor_Country']."'");
$res=DataBaseClass::getRow();
if(isset($res['Name'])){ 
    $Continent=$res['Name'];
    $Continent_Code=$res['Code'];
}else{
    $Continent='';
    $Continent_Code='';
}

$Continent_Countries=[];
DataBaseClass::Query("Select C.ISO2 from Country C"
         . " where C.Continent='$Continent_Code'");
foreach(DataBaseClass::getRows() as $row){
    $Continent_Countries[]=$row['ISO2'];
}
$sql=" 
select c.Name, c.Code,c.CodeScript, sum(case when al.Al_vOrder is null then 0 else 1 end)+1 Rank, replace(c.Special,'Sum','Best') Special,max(A.vOut) vOut from
(select min(A.vOrder) vOrder, D.Name,D.Code,D.CodeScript, replace(A.Special,'Mean','Average') Special
from Attempt A
join Command Com on A.Command=Com.ID
join CommandCompetitor CC on CC.Command=Com.ID
join Competitor C on C.ID=CC.Competitor
left outer join Country on Country.ISO2=C.Country
left outer join Continent on Continent.Code=Country.Continent
join Event E on E.ID=Com.Event
join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
join DisciplineFormat DF on E.DisciplineFormat=DF.ID
join Discipline D on D.ID=DF.Discipline and D.Status='Active'
where CC.Competitor=".$competitor['Competitor_ID']." and A.isDNF!=1  and A.Special is not null
and ([ext_where])
group by D.Name,D.Code,D.CodeScript, replace(A.Special,'Mean','Average')) c

left outer join
(select vCom.IDs , min(A.vOrder) Al_vOrder, D.Code Al_Code, replace(A.Special,'Mean','Average') Al_Special
from Attempt A
join Command Com on A.Command=Com.ID
join ( 
select Command.ID, GROUP_CONCAT(Competitor.ID order by Competitor.ID) IDs from Command 
join CommandCompetitor on CommandCompetitor.Command=Command.ID
join Competitor on CommandCompetitor.Competitor=Competitor.ID
group by Command.ID

) vCom on vCom.ID=Com.ID
join CommandCompetitor CC on Com.ID=CC.Command
join Competitor C on C.ID=CC.Competitor
left outer join Country on Country.ISO2=Com.vCountry
left outer join Continent on Continent.Code=Country.Continent
join Event E on E.ID=Com.Event
join DisciplineFormat DF on E.DisciplineFormat=DF.ID
join Discipline D on D.ID=DF.Discipline
where A.Special is not null
and ([ext_where])
group by  vCom.IDs  ,D.Code, replace(A.Special,'Mean','Average')
) al on c.vOrder > al.Al_vOrder and c.Code = al.Al_Code and c.Special=al.Al_Special

join (
select distinct vOut,vOrder from Attempt
)A on A.vOrder=c.vOrder

where c.vOrder is not null
group by Name,Code,CodeScript, c.Special

order by Name";

$ranks=[]; 
$events=[]; 

DataBaseClass::Query(str_replace('[ext_where]','true',$sql));
foreach(DataBaseClass::getRows() as $row){
    $events[$row['Code']]=['Name'=>$row['Name'],'CodeScript'=>$row['CodeScript']]; 
    $ranks[$row['Code']]['All'][$row['Special']]=['Rank'=>$row['Rank'],'vOut'=>$row['vOut']];
}

DataBaseClass::Query(str_replace('[ext_where]',"Com.vCountry='".$competitor['Competitor_Country']."'",$sql));
foreach(DataBaseClass::getRows() as $row){
    $ranks[$row['Code']]['Country'][$row['Special']]=['Rank'=>$row['Rank']];
}

DataBaseClass::Query(str_replace('[ext_where]',"Continent.Code='".$Continent_Code."'",$sql));
foreach(DataBaseClass::getRows() as $row){
    $ranks[$row['Code']]['Continent'][$row['Special']]=['Rank'=>$row['Rank']];
}

?>

<table class="no_border">
    <tr>
        <td>
            <?php if($competitor['Competitor_Avatar']){?>
                <img style="border-radius: 20px" src="<?= $competitor['Competitor_Avatar'] ?>" valign=top>
            <?php } ?>
        </td>
        <td>
<h1><?= $competitor['Competitor_Name'] ?></h1>  
<?php if($competitor['Competitor_Country']){ ?>
    <?= ImageCountry($competitor['Competitor_Country'], 50)?> <?= CountryName($competitor['Competitor_Country']) ?> (<?= $Continent ?>)
<?php } ?>  
<?php if ($competitor['Competitor_WCAID']){ ?>    
    &#9642; <a href="https://www.worldcubeassociation.org/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?></a>
<?php } ?>  
<?php if($competitor['Competitor_Email'] and CheckAccess('Competitor.Email')){ ?>
    &#9642; <a href='mailto:<?= $competitor['Competitor_Email'] ?>'><?= $competitor['Competitor_Email'] ?></a>
<?php } ?>  
        </td>
    </tr>
</table>
<hr class='hr_round'>
<h2><img src='<?= PageIndex()?>Image/Icons/record.png' width='20px'> <?= ml('Competitor.Rank.Title'); ?></h2>
<table>  
    <tr class='tr_title'>
        <td nowrap align='center'>
            <?= ml('Competitor.Rank.Table.Event'); ?>
        </td>
        
        <td align='center' nowrap>
           <?= ml('Competitor.Rank.Table.CountryRank'); ?>
        </td>
        <td align='center' nowrap>
           <?= ml('Competitor.Rank.Table.ContinentalRank'); ?>
        </td>
        <td align='center' nowrap> 
            <?= ml('Competitor.Rank.Table.WorldRank'); ?>
        </td>
        <td nowrap align='center'>
            <?= ml('Competitor.Rank.Table.Single'); ?>
        </td>
        <td nowrap align='right'>
            <?= ml('Competitor.Rank.Table.Average'); ?>
        </td>
        <td align='center' nowrap>
            <?= ml('Competitor.Rank.Table.WorldRank'); ?>
        </td>
        <td align='center' nowrap>
           <?= ml('Competitor.Rank.Table.ContinentalRank'); ?>
        </td>
        <td align='center' nowrap>
           <?= ml('Competitor.Rank.Table.CountryRank'); ?>
        </td>
    </tr>   
<?php foreach($events as $code=>$event){ ?>
    <tr>
        <td class='border-right-solid'>
            <?= ImageEvent($event['CodeScript'],20,$event['Name'] ) ?> <a href='<?= PageIndex() ?>Event/<?= $code ?>'><?= $event['Name'] ?></a>
        </td>
        
        
        
        <td align='center' width='30px'>
        <?php if($competitor['Competitor_Country']){
                    if(isset($ranks[$code]['Country']['Best']['Rank'])){ ?>
                        <?php $r=$ranks[$code]['Country']['Best']['Rank'] ?>
                        <span class='<?= $r<=10?'PB':'' ?>'><?= $r ?></span>
                <?php }else{ ?>
                    -
                <?php } ?>
        <?php } ?>
        </td>
        <td align='center' width='30px'>
        <?php if($Continent_Code){ ?>
            <?php $r=isset($ranks[$code]['Continent']['Best']['Rank'])?$ranks[$code]['Continent']['Best']['Rank']:'' ?>
            <span class='<?= $r<=10?'PB':'' ?>'><?= $r ?></span>
        <?php } ?>
        </td>
        <td  align='center' width='30px'>
            <?php $r=$ranks[$code]['All']['Best']['Rank'] ?>
            <span class='<?= $r<=10?'PB':'' ?>'><?= $r ?></span>
        </td>
        <td align='right' class=' border-left-dotted border-right-solid'>
            <b><?= isset($ranks[$code]['All']['Best']['vOut'])?$ranks[$code]['All']['Best']['vOut']:'' ?></b>
        </td>
        
        
        <td  align='right' width='100px' class=' border-right-dotted'>
            <b><?= isset($ranks[$code]['All']['Average']['vOut'])?$ranks[$code]['All']['Average']['vOut']:'' ?></b>
        </td>
        <td  align='center' width='30px'>
            <?php $r=isset($ranks[$code]['All']['Average']['Rank'])?$ranks[$code]['All']['Average']['Rank']:'' ?>
            <span class='<?= $r<=10?'PB':'' ?>'><?= $r ?></span>
        </td>
        <td align='center' width='30px'>
        <?php if($Continent_Code){ ?>
                <?php $r=isset($ranks[$code]['Continent']['Average']['Rank'])?$ranks[$code]['Continent']['Average']['Rank']:'' ?>
                <span class='<?= $r<=10?'PB':'' ?>'><?= $r ?></span>
        <?php } ?>
            </td>
        <td align='center' width='30px'>
        <?php if($competitor['Competitor_Country']){
                if(isset($ranks[$code]['Country']['Average']['Rank'])){ ?>
                    <?php $r=$ranks[$code]['Country']['Average']['Rank'] ?>
                    <span class='<?= $r<=10?'PB':'' ?>'><?= $r ?></span>
            <?php }else{ ?>
                -
            <?php } ?>
        <?php } ?>
        </td>
    </tr>
<?php } ?>
</table>
<hr class='hr_round'>

<?php 

DataBaseClass::FromTable('Competitor',"ID='".$competitor['Competitor_ID']."'");
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Command');
DataBaseClass::Where_current('Decline!=1');
DataBaseClass::Join_current('Event');
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('Event','Competition');
DataBaseClass::Where_current("WCA not like 't.%'");
DataBaseClass::OrderClear('Discipline', 'Name');
DataBaseClass::SelectPre('distinct D.ID Discipline_ID, '
        . 'D.Status  Discipline_Status, '
        . 'D.Code Discipline_Code, '
        . 'D.Name Discipline_Name, '
        . 'D.Competitors Discipline_Competitors ');

$disciplines=DataBaseClass::QueryGenerate(); ?>
<h2><?= ml('Competitor.Results.Title'); ?></h2>
<table class="discipline_result">
<?php foreach($disciplines as $discipline){
    
    DataBaseClass::FromTable('Discipline',"ID='".$discipline['Discipline_ID']."'");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Format');
    DataBaseClass::OrderClear('Format', 'Result');
    DataBaseClass::SelectPre('distinct F.Result,F.ExtResult,F.Attemption ');
    $types=array();
    $attemption=0;
    foreach(DataBaseClass::QueryGenerate() as $row){        
        $types[]=$row['Result'];
        if($row['ExtResult']){
            $types[]=$row['ExtResult'];    
        }
        $attemption=$attemption<$row['Attemption']?$row['Attemption']:$attemption;
    }
    
    foreach($types as $t=>$type){
        if($type=='Mean'){
            $types[$t]='Average';
        }
    }
    $types=array_unique($types);
    sort($types);
    ?>
<tr class="no_border">
    <td colspan='<?= $attemption+3+sizeof($types)?>'>
        <br>
            <?= ImageEvent($discipline['Discipline_CodeScript'],30, $discipline['Discipline_Name']) ?>
            <span class="<?= $discipline['Discipline_Status']=='Archive'?'archive':'' ?>">
                <?= $discipline['Discipline_Name'] ?>
            </span>
            <a href="<?= PageIndex()?>Records/all/<?= $discipline['Discipline_Code'] ?>"> <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/record.png"> <?= ml('Event.Records'); ?></a>
            <a href="<?= LinkDiscipline($discipline['Discipline_Code'])?>"> <img style="vertical-align: middle" width="20px"  src="<?= PageIndex()?>Image/Icons/rankings.png"><?= ml('Competition.Rankings'); ?></a>
        
    </td>        
</tr>        
        <tr class='tr_title'> 
            <td></td>
            <?php foreach($types as $type){ ?>
                <td class='attempt'>
                    <?= ml('Competitor.Result.Table.'.str_replace('Best','Single',$type)); ?>
                </td>
            <?php } ?>
            <?php for($i=sizeof($types);$i<3;$i++){ ?>    
                <td/>
            <?php } ?>
            <td style="text-align:left"><?= ml('Competitor.Result.Table.Competition');?></td>
            <td style="text-align:left"></td>

            <?php for($i=1;$i<=$attemption;$i++) {?>
            <td class="attempt">             
                <?php if($image=IconAttempt($discipline['Discipline_Code'],$i)){ ?>
                    <img src="<?= PageIndex() ?>/<?= $image ?>" width="20px">
                <?php }else{ ?>
                    <?= $i ?>
                <?php } ?>
            </td>
            <?php } ?>
        </tr>   
 
<?php             
 //foreach($discipline as $row){
    
DataBaseClass::FromTable('Competitor',"ID='".$competitor['Competitor_ID']."'");
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Command');
DataBaseClass::Where_current('Decline!=1');
DataBaseClass::Join_current('Event');
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Where_current("ID='".$discipline['Discipline_ID']."'");
DataBaseClass::Join('Event','Competition');
//DataBaseClass::OrderClear('Competition', 'ID Desc');
DataBaseClass::OrderClear('Competition', 'StartDate desc');
DataBaseClass::Order('Competition', 'EndDate desc');
DataBaseClass::Order('Event', 'Round desc');
$commands=DataBaseClass::QueryGenerate(true,false);


//usort($competitorEvents,'Competition_Sort');
$bestID=array();

foreach($types as $type){
    $format_arr=[$type];
    if($type=='Average'){
        $format_arr=['Mean','Average'];
    }
    
    DataBaseClass::FromTable('Competitor',"ID='".$competitor['Competitor_ID']."'");
    DataBaseClass::Join_current('CommandCompetitor');
    DataBaseClass::Join_current('Command');
    DataBaseClass::Where_current('Decline!=1');
    DataBaseClass::Join_current('Event');
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Where_current("ID='".$discipline['Discipline_ID']."'");
    DataBaseClass::Join('Event','Competition');
    DataBaseClass::Where_current("Unofficial=0");
    DataBaseClass::Join('Command','Attempt');        
    DataBaseClass::Where("A.Special in ('".implode("','",$format_arr)."')");
    DataBaseClass::Where_current("IsDNF=0");
    DataBaseClass::Limit("1");
    DataBaseClass::OrderClear("Attempt","vOrder");
    $bestID[]=DataBaseClass::QueryGenerate(false)['Attempt_ID']; 
}


    foreach($commands as $command){ 
        $attempts=array();
        for($i=1;$i<=$attemption;$i++) {
            $attempts[$i]="";
        }
        foreach(DataBaseClass::SelectTableRows("Format") as $format){
            $attempts[$format['Format_Result']]="";    
        }

        $is_attempt=false;
        DataBaseClass::FromTable('Attempt',"Command='".$command['Command_ID']."' ");
        foreach(DataBaseClass::QueryGenerate() as $attempt_row){
            $is_attempt=true;
            $attempt=$attempt_row['Attempt_vOut'];
            if($attempt_row['Attempt_Except']){
                $attempt="($attempt)";
            }

            if($attempt_row['Attempt_Attempt']){
               $attempts[$attempt_row['Attempt_Attempt']]= $attempt;
            }else{
                if($attempt_row['Attempt_Special']=='Mean'){
                    $type='Average';
                }else{
                    $type=$attempt_row['Attempt_Special'];
                }
               $attempts[$type]= $attempt; 
               $attempts_ID[$type]= $attempt_row['Attempt_ID'];
            }
        }
        
        $class=($command['Command_Place']<=3 and $command['Command_Place'])?"podium":"";?>

        <tr class="<?= $class ?>">
            <td class="number">
                <?php if($command['Command_Place']){ ?>
                    <?= $command['Command_Place'] ?>
                <?php } ?>
                <?php if(!$is_attempt){ ?>
                    <?= svg_green(12,'Upcoming competition') ?>
                <?php } ?>
            </td>
            <?php 
            foreach($types as $type){ 
                    $WRecord=-9;
                    $NRecord=-9;
                    $CRecord=-9;
                    $format_arr=[$type];
                    if($type=='Average'){
                        $format_arr=['Mean','Average'];
                    }
                    DataBaseClass::FromTable('Competition',"EndDate<='".$command['Competition_EndDate']."'");       
                    DataBaseClass::Where_current("Unofficial=0");
                    DataBaseClass::Join_current("Event");
                    DataBaseClass::Join_current("DisciplineFormat");
                    DataBaseClass::Join_current("Format");
                    DataBaseClass::Join("DisciplineFormat","Discipline");
                    DataBaseClass::Join("Event","Command");
                    DataBaseClass::Join("Command","Attempt");
                    DataBaseClass::Join("Command","CommandCompetitor");
                    DataBaseClass::Where('Discipline',"ID='".$command['Discipline_ID']."'");    
                    DataBaseClass::Where("A.Special in ('".implode("','",$format_arr)."')");
                    DataBaseClass::Where('A.isDNF = 0');
                    DataBaseClass::OrderClear('Attempt', 'vOrder');
                    DataBaseClass::SelectPre("distinct Com.ID Command, Com.vCountry, A.vOut, A.vOrder,A.Special,C.WCA ");

                    foreach(DataBaseClass::QueryGenerate() as $r){
                        if($WRecord==-9){
                            $WRecord=$r['vOut'];
                        }
                        if($NRecord==-9 and $r['vCountry'] and $r['vCountry']==$competitor['Competitor_Country']){
                            $NRecord=$r['vOut'];
                        }
                        if($CRecord==-9 and in_array($r['vCountry'],$Continent_Countries)){
                            $CRecord=$r['vOut'];
                        }            
                    }
                ?>
                <td class="attempt">
                   <?php if(isset($attempts[$type]) and !in_array($attempts[$type],array('DNF','DNS'))){ ?>
                        <nobr> 
                           <span class="<?= in_array($attempts_ID[$type],$bestID)?"PB":"" ?>">
                               <?=  $attempts[$type]; ?> 
                           </span>
                            <?php if($WRecord==$attempts[$type]){ ?>
                                <span class="message">WR</span>
                            <?php }elseif($CRecord==$attempts[$type]){ ?>
                                <span class="message">CR</span>
                            <?php }elseif($NRecord==$attempts[$type]){ ?>
                                <span class="message">NR</span>
                            <?php } ?>
                    </nobr>        
                   <?php } ?> 
               </td>  
            <?php } ?>
            <?php for($i=sizeof($types);$i<3;$i++){ ?>    
                <td/>
            <?php } ?>
            <td>
                <a href="<?= LinkEvent($command['Event_ID']) ?>">
                    <nobr><span class="<?= $command['Competition_Unofficial']?'unofficial':''?>"><?= $command['Competition_Name'] ?></span>
                        <?php if($command['Command_Video']){ ?>    
                            <a target=_blank" href="<?= $command['Command_Video'] ?>"><img class="video" src="<?= PageIndex()?>Image/Icons/Video.png"></a>
                        <?php } ?>
                    </nobr>
                </a>
            
            <?php if($discipline['Discipline_Competitors']>1){ ?>
            
                        <?php DataBaseClass::FromTable("Command","ID='".$command['Command_ID']."'") ;
                        DataBaseClass::Join_current("CommandCompetitor");
                        DataBaseClass::Join_current("Competitor");
                        DataBaseClass::Where_current("ID<>".$competitor['Competitor_ID']);
                        $competitors=DataBaseClass::QueryGenerate();
                        foreach($competitors as $competitor_com){ ?>
                                <br>
                                <nobr>
                                <?= svg_blue(12,'Teammate') ?>
                                <a href="<?= LinkCompetitor($competitor_com['Competitor_ID'],$competitor_com['Competitor_WCAID'])?>">
                                    <?= Short_Name($competitor_com['Competitor_Name']) ?>      
                                </a>
                                </nobr>      
                        <?php } ?>
                
             <?php }?>
            </td>
            <td class="attempt">
                    <nobr><?= str_replace(": ","",$command['Event_vRound']) ?></nobr> 
            </td>

            <?php if(!$is_attempt){ ?>
                <td  class="future" colspan="<?= $attemption ?>">
                    <nobr><?= date_range($command['Competition_StartDate'], $command['Competition_EndDate']) ?></nobr>
                </td>
            <?php }else{ ?>
                <?php for($i=1;$i<=$attemption;$i++) {?>
                <td class="attempt">
                    <nobr><?= $attempts[$i]; ?></nobr>
                </td>
                <?php } ?>

            <?php } ?>
        </tr>
    <?php } ?>
<?php } ?>
</table>


<?php if(CheckAccess('Competitor.Reload') and ($competitor['Competitor_WID'] or $competitor['Competitor_WCAID'])){ ?>
    <br>
    <div class='form2'>    
        <form method='POST' action='<?= PageAction('Competitor.Reload')?>'>
            id <?=$competitor['Competitor_ID'] ?> &#9642;
            user_id <a target='_blank' href="https://www.worldcubeassociation.org/api/v0/users/<?= $competitor['Competitor_WID'] ?>"><?= $competitor['Competitor_WID'] ?></a> &#9642;
            wca_id <a target='_blank' href="https://www.worldcubeassociation.org/api/v0/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?></a>
            <input Name="Competitor" hidden value="<?= $competitor['Competitor_ID'] ?>">
            <input type='submit' value='<?= ml('*.Reload',false); ?>'>
            <span class='message'><?= GetMessage('Competitor.Reload'); ?></span>
        </form>        
    </div>  
<?php } ?>    
    
<?= mlb('*.Reload')?>
<?= mlb('Competitor.Result.Table.Single'); ?>
<?= mlb('Competitor.Result.Table.Average'); ?> 
<?= mlb('Competitor.Result.Table.Sum'); ?>