<?php

$request= getRequest();
if(isset($request[1])){
    $type_filter=$request[1];
}else{
    $type_filter='none';
}
$competitor= getCompetitor(); 
if($competitor){
    DataBaseClass::Query("Select Ct.Name,Ct.Code from Country C"
             . " join Continent Ct on Ct.Code=C.Continent where C.ISO2='".$competitor->country_iso2."'");
    $res=DataBaseClass::getRow();
    if(isset($res['Name'])){ 
        $Continent=$res['Name'];
        $Continent_Code=$res['Code'];
    }else{
        $Continent='';
        $Continent_Code='';
    }

    $Countries_code=[];
    DataBaseClass::Query("Select ISO2,Continent from Country where Continent<>''");
    foreach(DataBaseClass::getRows() as $row){
        if($row['Continent']==$Continent_Code){
            $Countries_code[]=$row['ISO2'];
        }
    }
}
$extraWhere="";
?>

<h1><?= ml('Events.Title'); ?></h1>
<table class="table_info">
    <?php if(CheckAccess('Event.Add')){?>
    <tr>
        <td><i class="fas fa-plus-square"></i></td>
        <td><a href='<?= PageIndex()?>Event/Add'>Add Event</a></td>
    </tr>    
    <?php } ?>
    <tr>
        <td><i class="fas fa-filter"></i> <?= ml('Events.Filer'); ?></td>
        <td>
            <select onchange="document.location='<?= PageIndex() ?>Events/'+this.value">
                <option selected value=''><?= ml('Events.All.Title'); ?></option>
                <option <?= $type_filter=='team'?'selected':'' ?> value='Team'><?= ml('Events.Team.Title'); ?></option>
                <option <?= $type_filter=='puzzles'?'selected':'' ?> value='Puzzles'><?= ml('Events.Puzzles.Title'); ?></option>
                <option <?= $type_filter=='333cube'?'selected':'' ?> value='333Cube'><?= ml('Events.333Cube.Title'); ?></option>
                <option <?= $type_filter=='wcapuzzle'?'selected':'' ?> value='WCAPuzzle'><?= ml('Events.WCAPuzzle.Title'); ?></option>
                <option <?= $type_filter=='nonwcapuzzle'?'selected':'' ?> value='nonWCAPuzzle'><?= ml('Events.nonWCAPuzzle.Title'); ?></option>
                <option <?= $type_filter=='simple'?'selected':'' ?> value='Simple'><?= ml('Events.Simple.Title'); ?></option>
                <option <?= $type_filter=='nonsimple'?'selected':'' ?> value='nonSimple'><?= ml('Events.nonSimple.Title'); ?></option>
                <option <?= $type_filter=='inscpection20'?'selected':'' ?> value='Inscpection20'><?= ml('Events.Inscpection20.Title'); ?></option>
            </select>    
        </td>
    </tr>
</table> 

<?php $Competitor=getCompetitor();

if($type_filter=='simple'){
    $extraWhere=" and Simple=1";
}

if($type_filter=='nonsimple'){
    $extraWhere=" and Simple=0";
}

if($type_filter=='team'){
    $extraWhere=" and D.Competitors>1";
}
 
if($type_filter=='puzzles'){
    $extraWhere=" and coalesce(D.TNoodles,'') <>'' ";
}

if($type_filter=='wcapuzzle'){
    $extraWhere=" and coalesce(GlueScrambles,0)=1 ";
}

if($type_filter=='nonwcapuzzle'){
    $extraWhere=" and coalesce(GlueScrambles,0)<>1 ";
}

if($type_filter=='inscpection20'){
    $extraWhere=" and Inspection=20 ";
}


if($type_filter=='333cube'){
    $extraWhere=" and (TNoodle='333' or TNoodles='333') and GlueScrambles=1 ";
}



    DataBaseClass::Query("select D.Simple,D.ID,D.Code,D.CodeScript,D.Status,D.Name,D.Competitors,
    count(distinct C.ID) countCompetitors,
    count(distinct Cn.ID) countCompetitions,
    (sum(case when Com.Place>0 and Cn.ID is not null then 1 else 0 end)>0) AttemptExists
    from `Discipline` D
    left outer join `DisciplineFormat` DF on DF.Discipline=D.ID
    left outer join `Event` E on E.DisciplineFormat=DF.ID
    left outer join `Competition` Cn on Cn.ID=E.Competition And Cn.Unofficial=0  and Cn.WCA not like 't.%'
    left outer join `Command` Com on Com.Event=E.ID 
    left outer join `CommandCompetitor` CC on CC.Command=Com.ID  
    left outer join `Competitor` C on C.ID=CC.Competitor and Cn.WCA not like 't.%'
    where 1=1 ".(CheckAccess('Events.Arhive')?"": " and D.Status='Active' ") ." $extraWhere
    group by D.ID
    order by D.Status
    ,count(distinct C.ID) desc, count(distinct E.Competition) desc,  D.Name"); 
    $disciplines= DataBaseClass::getRows(); 
    
    DataBaseClass::Query("Select D.* from Discipline D"
                         . " Left outer join Regulation R on D.ID=R.Event"
                         . " where D.Status='Active'  and R.ID is null"); 
    foreach(DataBaseClass::getRows() as $row){ 
        $eventwithoutregulations[]=$row['CodeScript'];
    } 
    
    ?>
    <table class="table_new" width="80%">
        <thead>
        <tr>
            <td></td>
            <td><?= ml('Events.Table.Name') ?></td>
            <td class="table_new_right"><?= ml('Events.Table.Single') ?></td>
            <td class="table_new_right"><?= ml('Events.Table.Average') ?></td>
            <td class="table_new_center"><?= ml('Events.Table.Persons') ?></td>
            <td class="table_new_center"><?= ml('Events.Table.Competitions') ?></td>
        </tr>
        </thead>
        <tbody>
    <?php
    foreach( $disciplines as $d=>$discipline){
            $attempt_exists = $discipline['AttemptExists']; ?>
    <tr>
        <?php $Record=array();
        $BaseSql="Select A.vOut,Com.vCountry,A.Special from Attempt A "
                . " join Command Com on Com.ID=A.Command and A.Special in ('@Special') @Country"
                . " join Event E on E.ID=Com.Event "
                . " join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0 "
                . " join CommandCompetitor CC on CC.Command=Com.ID "
                . " join Competitor C on C.ID=CC.Competitor @Competitor" 
                . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat and DF.Discipline=".$discipline['ID'] 
                . " where A.Special in (select Result from Format F where F.ID=DF.Format union select ExtResult from Format F where F.ID=DF.Format)   "
                . " order by A.vOrder limit 1";

        $params=array("@Special","@Country","@Competitor");
        $values=array("","","");

        $values[0]="Best','Sum";
        DataBaseClass::Query(str_replace($params,$values,$BaseSql));
        $Record[0][0]=DataBaseClass::getRow();

        $values[0]="Average','Mean";
        DataBaseClass::Query(str_replace($params,$values,$BaseSql));
        $Record[0][1]=DataBaseClass::getRow();  ?>
        
        <td><?= ImageEvent($discipline['CodeScript'],1.5,$discipline['Name']) ?></td>    
        <td>
            <a href="<?= LinkDiscipline($discipline['Code']) ?>">
                <?= $discipline['Name'] ?>
            </a>
            <?php if($discipline['Status']=='Archive'){ ?>
                <i class="fas fa-ban"></i>
            <?php } ?>
        </td>
        <td class="table_new_right">
        <?php if(isset($Record[0][0]['vOut'])){
            $r=$Record[0][0]; ?>
                <?= $r['vOut'] ?>
        <?php } ?>
        </td> 

        <td class="table_new_right">  
        <?php if(isset($Record[0][1]['vOut'])){ 
            $r=$Record[0][1]; ?>
                <?= $r['vOut'] ?>
        <?php } ?>
        </td> 
        <td class="table_new_center">
            <?= $discipline['countCompetitions']?$discipline['countCompetitors']:'0' ?> 
        </td>
        <td class="table_new_center">     
            <?= $discipline['countCompetitions']?$discipline['countCompetitions']:'0' ?>
        </td>
    </tr>
<?php } ?>
    </tbody>
    </table>
    
    
