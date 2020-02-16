<?php 
DataBaseClass::FromTable('Discipline'); 
DataBaseClass::OrderClear('Discipline','Name'); 
DataBaseClass::Where_current("Status='Active'");    
$disciplines=DataBaseClass::QueryGenerate();
$Event = ObjectClass::getObject('PageEvent');

$ID=$Event['Discipline_ID'];
$Code=$Event['Discipline_Code'];

DataBaseClass::Query(""
        . " Select coalesce(max(A.Attempt),1) MaxAttempt"
        . " from Discipline D "
        . " left outer join DisciplineFormat DF on DF.Discipline=D.ID "
        . " left outer join Event E on E.DisciplineFormat=DF.ID "
        . " left outer join Command Com on Com.Event=E.ID"
        . " left outer join Attempt A on A.Command=Com.ID and A.Attempt is not null"
        . " where D.Code='$Code'"
        . " group by D.ID");

$Event['MaxAttempt'] = DataBaseClass::getRow()['MaxAttempt'];

DataBaseClass::Query(""
        . " Select F.ID "
        . " from Discipline D "
        . " join DisciplineFormat DF on DF.Discipline=D.ID "
        . " join Format F on DF.Format=F.ID "
        . " where D.Code='$Code'"
        . " and Result='Sum'");

$FormatSum=isset(DataBaseClass::getRow()['ID']);

$request=getRequest();
if(isset($request[2]) and $request[2]=='average'){
    $FilterAverage='Average';
}else{
    $FilterAverage='Single';
}

if(isset($request[3]) and $request[3]=='results'){
    $FilterResults='Results';
}else{
    $FilterResults='Persons';
}

if(isset($request[4])){
    $FilterCountry=DataBaseClass::Escape($request[4]);
}else{
    $FilterCountry='all';
}

$ScrambligCode='all_scr';

if($FormatSum){
    $FilterAverage ='Sum';
}
?>

<?php
function sort_by_vOrder($a,$b){
    if(!isset($a['vOrder']) or !isset($b['vOrder']))return false;
    return $a['vOrder']>$b['vOrder'];
}    
            
$Scrambling=[49=>2,59=>10];
 
DataBaseClass::Query("
    select count(distinct IDs) count, vCountry,'Single' type from(
    Select Com.vCountry, GROUP_CONCAT(C.ID order by C.ID) IDs
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and vCountry!='' and ((D.ID='$ID' and A.Special='Best')".       
(isset($Scrambling[$ID])?" or (A.Attempt=".$Scrambling[$ID]." and D.Code='$ScrambligCode' )":'').
")
group by Com.ID)t group by vCountry
union 
    select count(distinct IDs) count, 'All' vCountry,'Single' type from(
    Select 'All',GROUP_CONCAT(C.ID order by C.ID) IDs
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and ((D.ID='$ID' and A.Special='Best')".       
(isset($Scrambling[$ID])?" or (A.Attempt=".$Scrambling[$ID]." and D.Code='$ScrambligCode' )":'').
")
group by Com.ID)t
union
    select count(distinct IDs) count, vCountry,'Average' type from(
    Select Com.vCountry, GROUP_CONCAT(C.ID order by C.ID) IDs
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Mean','Average') and vCountry!='' and D.ID='$ID'
group by Com.ID)t group by vCountry
union 
select count(distinct IDs) count, 'All' vCountry,'Average' type from(
Select 'All',GROUP_CONCAT(C.ID order by C.ID) IDs
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Mean','Average') and D.ID='$ID'
group by Com.ID)t
union
    select count(distinct IDs) count, vCountry,'Sum' type from(
    Select Com.vCountry, GROUP_CONCAT(C.ID order by C.ID) IDs
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Sum') and vCountry!='' and D.ID='$ID'
group by Com.ID)t group by vCountry
union 
    select count(distinct IDs) count, 'All' vCountry,'Sum' type from(
    Select 'All',GROUP_CONCAT(C.ID order by C.ID) IDs
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Sum') and D.ID='$ID'
group by Com.ID)t");

$Countries=[];
foreach(DataBaseClass::getRows() as $country){
    if(!isset($Countries[$country['vCountry']])){
        $Countries[$country['vCountry']]=['Name'=>CountryName($country['vCountry']),'Single'=>0,'Average'=>0];
    }
    $Countries[$country['vCountry']][$country['type']]=$country['count'];
    
}
$Countries['All']['Name']='.';
function SortByCountry($a,$b){
    return $a['Name']>$b['Name'];
}
uasort($Countries,'SortByCountry');
$Countries['All']['Name']=ml('Event.Country.Select.All',false);

$Results=[];



if($FilterAverage=='Single'){
    DataBaseClass::Query("
    Select GROUP_CONCAT(C.ID order by C.ID) vCompetitorIDs, GROUP_CONCAT(C.Name order by C.ID) vName, Com.vCountry, Com.Video,
    A.vOut, A.vOrder, A.Attempt,
    Cn.WCA Competition_WCA,Cn.Name Competition_Name,Cn.Country Competition_Country,  E.Round,E.vRound, E.ID Event_ID,D.ID,D.Code,D.CodeScript
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on CC.Command=Com.ID
    Join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
where A.IsDNF=0 and A.IsDNS=0 and ((D.ID='$ID' and A.Attempt!=0)".       
(isset($Scrambling[$ID])?" or (A.Attempt=".$Scrambling[$ID]." and D.Code='$ScrambligCode' )":'').
")
    and '$FilterCountry' in ('all',vCountry)
    group by Com.ID,Cn.ID, A.ID  
    order by vOrder,vName");
    $Results=DataBaseClass::getRows();

}

if($FilterAverage=='Average'){
    $sql="
        Select GROUP_CONCAT(C.ID order by C.ID) vCompetitorIDs, GROUP_CONCAT(C.Name order by C.ID) vName, Com.vCountry,Com.Video,
        A.vOut, A.vOrder, 
        Cn.WCA Competition_WCA,Cn.Name Competition_Name,Cn.Country Competition_Country, E.Round,E.vRound, E.ID Event_ID";
    
    for($i=1;$i<=$Event['MaxAttempt'];$i++){
        $sql.=",case when A{$i}.Except then concat('(',A{$i}.vOut,')') else A{$i}.vOut end  Attempt{$i}";
    }
    
    $sql.="
        from `Attempt` A 
        join Command Com on Com.ID=A.Command
        join CommandCompetitor CC on CC.Command=Com.ID
        Join Competitor C on C.ID=CC.Competitor
        join Event E on E.ID=Com.Event
        join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
        join DisciplineFormat DF on DF.ID=E.DisciplineFormat
        join Discipline D on D.ID=DF.Discipline";

    for($i=1;$i<=$Event['MaxAttempt'];$i++){
        $sql.=" left outer join `Attempt` A{$i} on A{$i}.Command=A.Command and A{$i}.Attempt={$i}";
    }
    $sql.="
        where D.ID='$ID' and A.Special in('Average','Mean')
        and A.IsDNF=0 and A.IsDNS=0 
        and '$FilterCountry' in ('all',vCountry)
        group by Com.ID,Cn.ID, A.ID";
            
        for($i=1;$i<=$Event['MaxAttempt'];$i++){
            $sql.=",A{$i}.ID ";
        }
        $sql.="order by vOrder,vName";

    DataBaseClass::Query($sql);
    $Results=DataBaseClass::getRows(); 
}


if($FilterAverage=='Sum'){
    $sql="
        Select GROUP_CONCAT(C.ID order by C.ID) vCompetitorIDs, GROUP_CONCAT(C.Name order by C.ID) vName, Com.vCountry,Com.Video,
        A.vOut, A.vOrder, 
        Cn.WCA Competition_WCA,Cn.Name Competition_Name,Cn.Country Competition_Country, E.Round,E.vRound, E.ID Event_ID";
    
    for($i=1;$i<=$Event['MaxAttempt'];$i++){
        $sql.=",case when A{$i}.Except then concat('(',A{$i}.vOut,')') else A{$i}.vOut end  Attempt{$i}";
    }
    
    $sql.="
        from `Attempt` A 
        join Command Com on Com.ID=A.Command
        join CommandCompetitor CC on CC.Command=Com.ID
        Join Competitor C on C.ID=CC.Competitor
        join Event E on E.ID=Com.Event
        join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0
        join DisciplineFormat DF on DF.ID=E.DisciplineFormat
        join Discipline D on D.ID=DF.Discipline";

    for($i=1;$i<=$Event['MaxAttempt'];$i++){
        $sql.=" left outer join `Attempt` A{$i} on A{$i}.Command=A.Command and A{$i}.Attempt={$i}";
    }
    $sql.="
        where D.ID='$ID' and A.Special in('Sum')
        and A.IsDNF=0 and A.IsDNS=0 
        and '$FilterCountry' in ('all',vCountry)
        group by Com.ID,Cn.ID, A.ID";
           for($i=1;$i<=$Event['MaxAttempt'];$i++){
            $sql.=",A{$i}.ID ";
        }
        $sql.="order by vOrder,vName";

    DataBaseClass::Query($sql);
    $Results=DataBaseClass::getRows(); 
}

if($FilterResults!='Results'){
    $exceptAttempCommand=[];
    foreach($Results as $r=>$Result){
        if(in_array($Result['vCompetitorIDs'],$exceptAttempCommand)){
           unset($Results[$r]); 
        }else{
            $exceptAttempCommand[]=$Result['vCompetitorIDs'];
        }
    }
} ?>
<h1 class="<?= $Event['Discipline_Status'] ?>">
    <?= ImageEvent($Event['Discipline_CodeScript'],50) ?>
    <?= $Event['Discipline_Name'] ?> / <?= ml('Event.Rankings') ?>
</h1>
<?php if($Event['Discipline_Status']=='Archive'){ ?>
<h2>
    <i class="fas fa-angle-double-right"></i> <?=  ml('Event.Archive.Title') ?>
</h2>   
<?php } ?>
<table width="100%"><tr><td>     
    <table class="table_info">
        <form name="Filter">
        <tr>
            <td>Type</td>
            <td>
                <?php if(!$FormatSum){
                    foreach(['Single','Average'] as $type){ ?> 
                        <p>
                            <?php if($type==$FilterAverage){ ?>
                            <input hidden value="<?= $type ?>" type="radio" class='FilterAverage' checked ID="FilterAverage_<?= $type ?>" name="FilterAverage">
                                <i class="far fa-check-square"></i> <?= ml('Event.Filter.'.$type) ?>
                            <?php }else{  ?>
                                <input hidden value="<?= $type ?>" type="radio" class='FilterAverage' ID="FilterAverage_<?= $type ?>" name="FilterAverage" onchange="reload();">
                                <a href="#"><i class="far fa-square"></i> <label  onclick="reload();" for="FilterAverage_<?= $type ?>"><?= ml('Event.Filter.'.$type) ?></label></a>
                            <?php } ?>
                        </p>
                    <?php } ?>    
                <?php } ?>    
            </td>
        </tr>
        <tr>
            <td>Show</td>
            <td>
                <?php foreach(['Persons','Results'] as $type){ ?> 
                    <p>
                        <?php if($type==$FilterResults){ ?>
                            <input hidden value="<?= $type ?>" type="radio" class='FilterResults' checked ID="FilterResults_<?= $type ?>" name="FilterResults" onchange="reload();">
                            <i class="far fa-check-square"></i> <?= ml('Event.Filter.'.$type) ?>
                        <?php }else{ ?>
                            <input hidden value="<?= $type ?>" type="radio" class='FilterResults'  ID="FilterResults_<?= $type ?>" name="FilterResults" onchange="reload();">
                            <a href="#"><i class="far fa-square"></i> <label  onclick="reload();" for="FilterResults_<?= $type ?>"><?= ml('Event.Filter.'.$type) ?></label></a>
                        <?php } ?>    
                    </p>
                <?php } ?>   
            </td>
        </tr>
        <tr>
            <td>Country</td>
            <td>
               <select ID="FilterCountry" onchange="reload();">
                    <?php foreach($Countries as $countryName=>$countryAttempts){ ?>
                    <option value="<?= $countryName ?>" <?= strtolower($countryName)==strtolower($FilterCountry)?'selected':'' ?> >
                        <?= $countryAttempts['Name'] ?> 
                        <?php if ($FormatSum){ ?>
                            [ <?= $countryAttempts['Sum'] ?> ]                 
                        <?php } else { ?>
                             [ <?= $countryAttempts['Average'] ?> / <?= $countryAttempts['Single'] ?> ] 
                        <?php } ?>
                    </option>
                    <?php } ?>
                </select> 
            </td>
        </tr>
        <tr>
            <td>Extra event</td>
            <td>
                <select ID="FilterEvent" onchange="reload();">
                <?php foreach($disciplines as $discipline_row){ ?>   
                    <option value="<?= $discipline_row['Discipline_Code'] ?>" <?= strtolower($discipline_row['Discipline_Code'])==$Code?'selected':''?> >
                        <?= $discipline_row['Discipline_Name'] ?>
                    </option>
                <?php } ?>    
                </select>                
            </td>
        </tr>  
        
        <script>
        function reload(){
            let str = [];

            var FilterCountry=$('#FilterCountry').val();
            var FilterResults=$('.FilterResults:checked').val();
            var FilterEvent=$('#FilterEvent').val();

            <?php if($FilterAverage!='Sum'){ ?>
            var FilterAverage=$('.FilterAverage:checked').val();
            var url= '<?= PageIndex() ?>Event/'+FilterEvent+'/'+FilterAverage +'/'+ FilterResults +'/' + FilterCountry ;
            <?php }else{ ?>
                var url= '<?= PageIndex() ?>Event/'+FilterEvent+'/sum/'+ FilterResults +'/' + FilterCountry ;
            <?php } ?>
            location.href = url;
        }    
        </script>
    </form>
    </table>
    </td><td>
<?= EventBlockLinks($Event,'rankings'); ?>
    </td>
    </tr>
</table>    

<h2>
    <?php if($FilterCountry!='all') { ?>
        <?= CountryName($FilterCountry); ?>  
    <?php }else{ ?>
        <?= ml('Event.Country.Title.All'); ?>
    <?php } ?>    
    &#9642;
    <?= ml('Event.Filter.'.$FilterAverage) ?> &#9642;
    <?= ml('Event.Filter.'.$FilterResults) ?>
</h2>
<table class='table_new' width="80%">
    <thead>
    <tr>
        <td/>
        <td><?= ml('Event.Table.Competitor')?></td>
        <td class="table_new_right"><?= ml('Event.Table.'.$FilterAverage); ?></td>
        <td/>
        <td><nobr><?= ml('Event.Table.Country')?></nobr></td>
       <td><?= ml('Event.Table.Competition')?></td>
        <?php if($FilterAverage=='Average'){ ?>
            <td class="table_new_center" colspan="<?=$Event['MaxAttempt'] ?>">
                 Solves
             </td>
        <?php } ?>
        <?php if($FilterAverage=='Sum'){ ?>
             <?php if($Event['Discipline_Codes']){ ?>                
                <?php for($i=0;$i<$Event['MaxAttempt'];$i++) {?>
                <td class="table_new_center">             
                    <span class=" cubing-icon event-<?= explode(",",$Event['Discipline_Codes'])[$i]?>"></span>
                </td>
                <?php } ?>
            <?php }else{ ?>
                <td class="table_new_center" colspan="<?= $attemption ?>">
                    Solves
                </td>
            <?php } ?>
        <?php } ?>
        
    </tr>
    </thead>
<?php 
$n=0; $fl=false; $prev=0;
    foreach($Results as $Result){ 
        $n++;
        $fl=($prev!==$Result['vOut']);
        if($fl){
            $new=$n;
        }
        $prev=$Result['vOut']; ?>    
    <tr>
        <td>
            <?= $new ?>
        </td>
        <td>
            <?php
               $Competitors_Name=Explode(",",$Result['vName']);
               $Competitors_ID=Explode(",",$Result['vCompetitorIDs']);
               $competitors=[];
            ?>        
            <?php foreach($Competitors_Name as $i=>$Competitor_Name){ 
                $Competitor_Name=trim($Competitor_Name);?>
                <?php ob_start(); ?>
                <p><a href='<?= LinkCompetitor( trim($Competitors_ID[$i]) )?>'><?=  $Competitor_Name ?></a></p>
                <?php $competitors[]=ob_get_clean(); ?>
            <?php } ?>
            <?= implode("",$competitors); ?>        
        </td>
        <td class="table_new_bold table_new_right">
            <?=  $Result['vOut'] ?>
        </td>
        <td>
            <?php if($Result['Video']){?>    
                <a target=_blank" href="<?= $Result['Video'] ?>"><i class="fas fa-video"></i></a>
            <?php } ?>
        </td>
        <td> <nobr><?php if($Result['vCountry']){ ?>
            <?= ImageCountry($Result['vCountry'])?>
                <?= CountryName($Result['vCountry']) ?>
            <?php }else{ ?>
                -
            <?php } ?></nobr>
       </td>                                 
        <td>
            <nobr>
                <?= ImageCountry($Result['Competition_Country'])?>
                <a href="<?= LinkEvent($Result['Event_ID']) ?>"><?=  $Result['Competition_Name'] ?></a>
                <?php if($FilterResults=='Results'){?>
                    <?php if($FilterAverage=='Single' and $ID!=$Result['ID']){ ?> 
                        <?= ImageEvent($Result['CodeScript'],20); ?>       
                    <?php } ?>
                <?php }else{ ?>
                    <?php if($FilterAverage=='Single' and $ID!=$Result['ID']){?>        
                        <?= ImageEvent($Result['CodeScript'],20); ?>       
                    <?php } ?>
                <?php } ?>    
            </nobr>
        </td>
        <?php if($FilterAverage=='Average'){
            for($i=1;$i<=$Event['MaxAttempt'];$i++){ ?>
                <td class="table_new_right">
                    <?php if(strpos($Result['Attempt'.$i],"(")!==false){ ?>
                        <span class='table_new_except table_new_attempt'><?= trim(str_replace(['(',')'],'',$Result['Attempt'.$i])) ?></span>
                    <?php }else{ ?>
                        <span class='table_new_attempt'><?= $Result['Attempt'.$i] ?></span>
                    <?php } ?>
                </td>
            <?php } ?>
        <?php } ?>
        <?php if($FilterAverage=='Sum'){
            for($i=1;$i<=$Event['MaxAttempt'];$i++){ ?>
                <td class="table_new_right">
                    <?= $Result['Attempt'.$i] ?>
                </td>
            <?php }    
        } ?>
    </tr>    
<?php } ?>
</table>    
 
<?= mlb('Event.Table.Competition')?>
<?= mlb('Event.Table.Round')?>
<?= mlb('Event.Table.Attempt')?>
<?= mlb('Event.Table.Average'); ?>
<?= mlb('Event.Table.Single'); ?>
<?= mlb('Event.Table.Sum'); ?>
<?= mlb('Event.Filter.Average') ?>
<?= mlb('Event.Filter.Single') ?>
<?= mlb('Event.Filter.Sum') ?>
<?= mlb('Event.Filter.Persons') ?>
<?= mlb('Event.Filter.Results') ?>
<?= mlb('Event.Country.Select.All'); ?>
<?= mlb('Event.Country.Title.All'); ?>
<?= mlb('Event.Competition.Past') ?>
<?= mlb('Event.Competition.Upcoming') ?>
<?=  mlb('Event.Archive.Title') ?>