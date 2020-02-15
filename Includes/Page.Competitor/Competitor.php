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

DataBaseClass::Query(str_replace('[ext_where]',"Com.vCountry<>'' and Continent.Code='".$Continent_Code."'",$sql));
foreach(DataBaseClass::getRows() as $row){
    $ranks[$row['Code']]['Continent'][$row['Special']]=['Rank'=>$row['Rank']];
}

?>
<h1><?= ImageCountry($competitor['Competitor_Country'])?> <?= $competitor['Competitor_Name'] ?></h1>  
                

<?php 
DataBaseClass::Query("Select * from Delegate where WID='".$competitor['Competitor_WID']."' and WID is not null");
$delegate=DataBaseClass::getRow(); ?>

<table class="table_info">
    <tr>
        <td><?= ml('Competitor.Country') ?></td>        
        <td><?= CountryName($competitor['Competitor_Country']) ?></td>
    </tr>
    <tr> 
        <td><?= ml('Competitor.Continent') ?></td>        
        <td><?= $Continent ?></td>
    </tr>
    <?php if ($competitor['Competitor_WCAID']){ ?>    
    <tr>
        <td>WCA ID</td>
        <td><a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?> <i class="fas fa-external-link-alt"></i></a></td>
    </tr>
    <?php } ?>  
    <?php if($competitor['Competitor_Email'] and CheckAccess('Competitor.Email')){ ?>
    <tr>
        <td>Email <i class="fa fa-crown"></i></td>
        <td><a href='mailto:<?= $competitor['Competitor_Email'] ?>'><i class="far fa-envelope"></i> <?= $competitor['Competitor_Email'] ?></a></td>
    </tr>
    <?php } ?>  
    <?php if($delegate){ ?>
        <td><?= ml('Competitor.Delegate') ?></td>            
        <td><a href="<?= LinkDelegate($delegate['WCA_ID'])?>"><?= ml('Delegate.'.$delegate['Status']) ?></a></td>
    <?php } ?>
    
</table>
<br>
<h2><?= ml('Competitor.Rank.Title'); ?></h2>
<table class="table_new" width="80%">  
    <thead>
    <tr>
        <td>
            <?= ml('Competitor.Rank.Table.Event'); ?>
        </td> 
        <td class="table_new_right" width="10%">
           NR
        </td>
        <td class="table_new_right" width="10%">
           CR
        </td>
        <td class="table_new_right" width="10%"> 
            WR
        </td>
        <td class="table_new_attempt" width="10%">
            <?= ml('Competitor.Rank.Table.Single'); ?>
        </td>
        <td class="table_new_attempt" width="10%">
            <?= ml('Competitor.Rank.Table.Average'); ?>
        </td>
        <td class="table_new_right" width="10%">
            WR
        </td>
        <td class="table_new_right" width="10%">
           CR
        </td>
        <td class="table_new_right" width="10%">
           NR
        </td>
        <td/>
    </tr>   
    </thead>
    <tbody>
<?php foreach($events as $code=>$event){ ?>
    <tr>
        <td>
            <a href='<?= PageIndex() ?>Event/<?= $code ?>'>
                <nobr>
                    <span style=" font-size:1.3em;"><?= ImageEvent($event['CodeScript'],25,$event['Name'] ) ?></span> 
                    <?= $event['Name'] ?>
                </nobr>    
            </a>
        </td>
        <td  class="table_new_right">
            <?php if($competitor['Competitor_Country']){
                    if(isset($ranks[$code]['Country']['Best']['Rank'])){ ?>
                        <?= $ranks[$code]['Country']['Best']['Rank'] ?>
                <?php }else{ ?>
                    -
                <?php } ?>
            <?php } ?>
        </td>
        <td  class="table_new_right">
        <?php if($Continent_Code){ ?>
            <?php $r=isset($ranks[$code]['Continent']['Best']['Rank'])?$ranks[$code]['Continent']['Best']['Rank']:'' ?>
            <?= $r?$r:'-' ?>
        <?php } ?>
        </td>
        <td  class="table_new_right">
            <?php $r=$ranks[$code]['All']['Best']['Rank'] ?>
            <?= $r?$r:'-' ?>
        </td>
        <td class="table_new_attempt table_new_bold">
            <?= isset($ranks[$code]['All']['Best']['vOut'])?$ranks[$code]['All']['Best']['vOut']:'' ?>
        </td>
        
        
        <td class="table_new_attempt table_new_bold">
            <?= isset($ranks[$code]['All']['Average']['vOut'])?$ranks[$code]['All']['Average']['vOut']:'' ?>
        </td>
        <td  class="table_new_right">
            <?php $r=isset($ranks[$code]['All']['Average']['Rank'])?$ranks[$code]['All']['Average']['Rank']:'' ?>
            <?= $r?$r:'-' ?>
        </td>
        <td class="table_new_right">
        <?php if($Continent_Code){ ?>
                <?php $r=isset($ranks[$code]['Continent']['Average']['Rank'])?$ranks[$code]['Continent']['Average']['Rank']:'' ?>
                <?= $r?$r:'-' ?>
        <?php } ?>
        </td>
        <td class="table_new_right">
        <?php if($competitor['Competitor_Country']){
                if(isset($ranks[$code]['Country']['Average']['Rank'])){ ?>
                    <?php $r=$ranks[$code]['Country']['Average']['Rank'] ?>
                    <?= $r ?>
            <?php }else{ ?>
                    -
            <?php } ?>        
        <?php } ?>
        </td>
        <td/>
    </tr>
<?php } ?>
</tbody>
</table>
<br>

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
        . 'D.Code Discipline_Code,D.CodeScript Discipline_CodeScript,D.Codes Discipline_Codes, '
        . 'D.Name Discipline_Name, '
        . 'D.Competitors Discipline_Competitors ');

$disciplines=DataBaseClass::QueryGenerate();?>
<h2><?= ml('Competitor.Results.Title'); ?></h2>
<?php foreach($disciplines as $d=>$discipline){
    
    
    
    DataBaseClass::FromTable('Competitor',"ID='".$competitor['Competitor_ID']."'");
    DataBaseClass::Join_current('CommandCompetitor');
    DataBaseClass::Join_current('Command');
    DataBaseClass::Where_current('Decline!=1');
    DataBaseClass::Join_current('Event');
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Where_current("ID='".$discipline['Discipline_ID']."'");
    DataBaseClass::Join('Event','Competition');
    DataBaseClass::Join('Command','Attempt');        
    
    $types=array();
    $attemption=0;
    foreach(DataBaseClass::QueryGenerate() as $a){
        if($a['Attempt_Special']  and !in_array($a['Attempt_Special'],$types)){
            $types[]=$a['Attempt_Special'];
        }
        if(is_numeric($a['Attempt_Attempt']) and $a['Attempt_Attempt']>$attemption){
            $attemption=$a['Attempt_Attempt'];
        }
    }
   
   /* 
    
    
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
    */
    
    
    foreach($types as $t=>$type){
        if($type=='Mean'){
            $types[$t]='Average';
        }
    }
    $types=array_unique($types);
    sort($types);
    $types= array_reverse($types);
    ?>
<hr><br>
<h2><?= ImageEvent($discipline['Discipline_CodeScript'],1, $discipline['Discipline_Name']) ?>
        <a href="<?= LinkDiscipline($discipline['Discipline_Code'])?>"> <?= $discipline['Discipline_Name'] ?></a>
        <?php if($discipline['Discipline_Status']=='Archive'){ ?>
        <i class="fas fa-angle-double-right"></i> <?= ml('Competitor.Event.Archive')?>
    <?php } ?>       
</h2>    
<table width="90%" class="table_new">
<thead>           
        <tr> 
            <td><?= ml('Competitor.Result.Table.Competition');?></td>
            <td></td>
            <td class="table_new_right"><?= ml('Competitor.Result.Table.Place');?></td>
            <?php foreach($types as $type){ ?>
                <td class="table_new_right">
                    <?= ml('Competitor.Result.Table.'.str_replace('Best','Single',$type)); ?>
                </td>
                <td></td>
            <?php } ?>
            <?php for($i=sizeof($types);$i<2;$i++){ ?>    
                <td></td><td></td>
            <?php } ?>

             <?php if($discipline['Discipline_Codes']){ ?>                
                <?php for($i=0;$i<$attemption;$i++) {?>
                <td class="table_new_center">             
                    <span class=" cubing-icon event-<?= explode(",",$discipline['Discipline_Codes'])[$i]?>"></span>
                </td>
                <?php } ?>
            <?php }else{ ?>
                <td class="table_new_center" colspan="<?= $attemption ?>">
                    <?= ml('Competitior.Solves') ?>
                </td>
            <?php } ?>
        </tr>   
 </thead>
 <tbody>
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
    #DataBaseClass::Where_current("IsDNF=0");
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
            $attempt=trim($attempt_row['Attempt_vOut']);
            if($attempt_row['Attempt_Except']){
                $attempt="<span class='table_new_except table_new_attempt'>$attempt</span>";
            }else{
                $attempt="<span class='table_new_attempt'>$attempt</span>";
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
        } ?>
       
        <tr>
            <td><nobr>
                 <?php if(!$is_attempt){ ?>
                    <i class="fas fa-hourglass-start"></i> 
                 <?php } ?>
                 <!--<?= date_range($command['Competition_StartDate'], $command['Competition_EndDate']) ?>-->
                    <a href="<?= LinkEvent($command['Event_ID']) ?>"><?= $command['Competition_Name'] ?></a>
                    <?php if($command['Competition_Unofficial'] and $is_attempt){ ?>
                        <i title="<?= ml('Competitor.Competition.Unofficial',false) ?>" class="fas fa-exclamation-triangle"></i>
                    <?php } ?>
                    <?php if($command['Command_Video']){ ?>    
                        <a target=_blank" href="<?= $command['Command_Video'] ?>"><i class="fas fa-video"></i></a>
                    <?php } ?>
            <?php if($discipline['Discipline_Competitors']>1){ ?>
            
                        <?php DataBaseClass::FromTable("Command","ID='".$command['Command_ID']."'") ;
                        DataBaseClass::Join_current("CommandCompetitor");
                        DataBaseClass::Join_current("Competitor");
                        DataBaseClass::Where_current("ID<>".$competitor['Competitor_ID']);
                        $competitors=DataBaseClass::QueryGenerate();
                        foreach($competitors as $competitor_com){ ?>
                                <br>
                                <i class="fas fa-user-plus"></i>
                                <a href="<?= LinkCompetitor($competitor_com['Competitor_ID'],$competitor_com['Competitor_WCAID'])?>">
                                    <?= Short_Name($competitor_com['Competitor_Name']) ?>      
                                </a>
                        <?php } ?>
                
             <?php }?>
            </nobr></td>
            <td>
                <?= str_replace(": ","",$command['Event_vRound']) ?> 
            </td>
            <td class="table_new_right">
                <?php if($command['Command_Place']){ ?>
                    <?= $command['Command_Place'] ?>
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
                    DataBaseClass::Where('A.isDNS = 0');
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
                
                   <?php if(isset($attempts[$type])){ ?>
                        <?php if($command['Competition_Unofficial']){ ?>
                            <td class="table_new_right">
                                     <?=  $attempts[$type]; ?>
                            </td>  
                        <?php }else{  ?>
                            <td class="table_new_right table_new_bold <?= (isset($attempts_ID[$type]) and in_array($attempts_ID[$type],$bestID))?"table_new_PB":"" ?>"> 
                               <?=  $attempts[$type]; ?>
                            </td>  
                        <?php } ?>
                        <td>
                            <?php if($WRecord==$attempts[$type]){ ?>
                                WR
                            <?php }elseif($CRecord==$attempts[$type]){ ?>
                                CR
                            <?php }elseif($NRecord==$attempts[$type]){ ?>
                                NR
                            <?php } ?>
                        </td>
                   <?php }else{ ?>
                        <td/><td/>
                   <?php } ?>
               
            <?php } ?>
            <?php for($i=sizeof($types);$i<2;$i++){ ?>    
                <td/><td/>
            <?php } ?>
            <?php if(!$attemption){ ?>
                <td class="table_new_attempt"></td>
            <?php } ?>
            <?php for($i=1;$i<=$attemption;$i++) {?>
            <td class="table_new_attempt">
                <?= $attempts[$i]; ?>
            </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>    
    </table>
<?php } ?>



<?php if(CheckAccess('Competitor.Reload') and ($competitor['Competitor_WID'] or $competitor['Competitor_WCAID'])){ ?>
    <h3><i class="fas fa-crown"></i> Reload information</h3>
    <form method='POST' action='<?= PageAction('Competitor.Reload')?>'>
    <input Name="Competitor" hidden value="<?= $competitor['Competitor_ID'] ?>">
    <table class="table_info">
        <tr>
            <td>id</td>
            <td><?=$competitor['Competitor_ID'] ?></td>
        </tr>   
        <tr>
            <td>user_id</td>
            <td><a target='_blank' href="https://www.worldcubeassociation.org/api/v0/users/<?= $competitor['Competitor_WID'] ?>"><?= $competitor['Competitor_WID'] ?> <i class="fas fa-external-link-alt"></i></a></td>
        </tr> 
        <tr>
            <td>wca_id</td>
            <td><a target='_blank' href="https://www.worldcubeassociation.org/api/v0/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?> <i class="fas fa-external-link-alt"></a></td>
        </tr> 
        <tr>
            <td></td>
            <td><button><i class="fas fa-sync-alt"></i> Reload</button></td>
        </tr>    
        <?php $message=GetMessage('Competitor.Reload');
        if($message){ ?>
        <tr>
            <td></td>
            <td><?= $message ?></td>
         </tr>       
        <?php } ?>
    </table>
    </form>
<?php } ?>    
