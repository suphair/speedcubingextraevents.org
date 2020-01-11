<?php includePage('Navigator') ?>
<?php $request=request();
if(isset($request[1]) and substr($request[1],0,1)!=='_'){
    $country_filter= DataBaseClass::Escape($request[1]);
    
    DataBaseClass::Query("Select * from Country where ISO2='". strtoupper($country_filter)."'");
    if(sizeof(DataBaseClass::getRows())!==1){
        $country_filter='all'; 
    }
    
}else{
    $country_filter='all';    
}    

if(isset($request[1]) and substr($request[1],0,1)=='_'){
    $continent_filter= str_replace("_","",DataBaseClass::Escape($request[1]));
}else{
    $continent_filter='';    
}

if(isset($request[2])){
   $DisciplineCode=DataBaseClass::Escape($request[2]);
}else{
    $DisciplineCode=''; 
}
$continents=[];
$Continents=[];
DataBaseClass::Query("Select * from Continent where Code <>'' order by Name");
foreach(DataBaseClass::getRows() as $row){
    $Continents[strtolower($row['Code'])]=$row['Name'];
}

if($continent_filter and !isset($Continents[$continent_filter])){
    $continent_filter='';
    $country_filter='all';    
}

$Countries_Continent=[];
$Countries_code=[];
DataBaseClass::Query("Select ISO2,Continent from Country where Continent<>''");
foreach(DataBaseClass::getRows() as $row){
    $Countries_Continent[$row['ISO2']]=$row['Continent'];
    if(strtolower($row['Continent'])==$continent_filter){
        $Countries_code[]=$row['ISO2'];
    }
}
DataBaseClass::FromTable("Discipline","Code='".$DisciplineCode."'");
DataBaseClass::Join_current("DisciplineFormat");
DataBaseClass::Join_current("Format");

$DiscipineFilter= DataBaseClass::QueryGenerate(false);
if(!isset($DiscipineFilter['Discipline_ID'])){
    $DisciplineCode='';
}
DataBaseClass::FromTable('Discipline'); 
DataBaseClass::Where_current("Status='Active'");
$disciplines=DataBaseClass::QueryGenerate(); ?>
<div class="line">
    <a class="<?= (!$DisciplineCode and !$country_filter)?"line_select":""?>" title="World records" href="<?= PageIndex()?>Records"><?= ImageCountry('', 25); ?></a>
    <?php foreach($disciplines as $discipline_row){ ?>   
        <a class="<?= strtolower($discipline_row['Discipline_Code'])==$DisciplineCode?"line_select":""?>" title="<?= $discipline_row['Discipline_Name'] ?>" href="<?= PageIndex()?>Records/<?= $continent_filter?"_$continent_filter":$country_filter?>/<?= $discipline_row['Discipline_Code']?>"><?= ImageEvent($discipline_row['Discipline_CodeScript'],25) ?></a> 
    <?php } ?>
</div>
<hr>
<?php
    DataBaseClass::FromTable('Competition');   
    DataBaseClass::OrderClear('Competition', 'EndDate');
    #DataBaseClass::Where_current("WCA not like 't.%'");
    $competitions= DataBaseClass::QueryGenerate();
    $res=array(); 
    $results=array();
    $formats=array();
    
    foreach($competitions as $competition){      
        DataBaseClass::FromTable("Competition","ID='".$competition['Competition_ID']."'");
        DataBaseClass::Where_current('Unofficial=0');    
        DataBaseClass::Join_current("Event");
        DataBaseClass::Join_current("DisciplineFormat");
        DataBaseClass::Join_current("Format");
        DataBaseClass::Join("DisciplineFormat","Discipline");
        DataBaseClass::Where_current("Status='Active'");
        DataBaseClass::Join("Event","Command");
        DataBaseClass::Join("Command","Attempt");
        DataBaseClass::Join("Command","CommandCompetitor");
        DataBaseClass::Join("CommandCompetitor","Competitor");
        if($DisciplineCode){
            DataBaseClass::Where('Discipline',"Code='$DisciplineCode'");    
        }
        DataBaseClass::Where('A.Special in (F.Result,F.ExtResult)');
        DataBaseClass::Where('A.isDNF = 0');
        DataBaseClass::Where("Com.vCountry<>''");
        if($country_filter!='all'){
            DataBaseClass::Where('Command',"vCountry='".strtoupper($country_filter)."'");    
        }
        if($continent_filter){
            DataBaseClass::Where('Command',"vCountry in('".implode("','",$Countries_code)."')");    
        }
        
        DataBaseClass::OrderClear('Discipline', 'Code');
        DataBaseClass::Order('Attempt', 'vOrder');
        foreach(DataBaseClass::QueryGenerate() as $n=>$row){
            $formats[$row['Attempt_Special']]=1;
            $MS=$row['Attempt_vOrder'];
            $row['Attempt_Special']=str_replace('Mean','Average',$row['Attempt_Special']);
            if(!isset($cuts[$row['Discipline_Code']][$row['Attempt_Special']])
                or $MS<$cuts[$row['Discipline_Code']][$row['Attempt_Special']]){
                    $cuts[$row['Discipline_Code']][$row['Attempt_Special']]=$MS;
                $results[$competition['Competition_EndDate']][]=$row;
            }
        }
    }
    
    $results= array_reverse($results);
    
    foreach($results as $n=>$comp){        
        $results[$n]=array_reverse($comp);
    } 
       
    $countries=array();
    DataBaseClass::FromTable("Command");
    DataBaseClass::Join_current("Event");
    DataBaseClass::Join_current("DisciplineFormat");
    DataBaseClass::Join_current("Discipline");    
    DataBaseClass::Where_current("Status='Active'");
    DataBaseClass::Join("DisciplineFormat","Format");
    DataBaseClass::Join("Event","Competition");
    DataBaseClass::Where_current("WCA not like 't.%'");
    DataBaseClass::Join("Command","Attempt");
    DataBaseClass::Where('A.Special in (F.Result,F.ExtResult)');
    DataBaseClass::Where('A.isDNF = 0');
    if($DisciplineCode){
        DataBaseClass::Where("Discipline","Code='".$DisciplineCode."'");
    }
    foreach(DataBaseClass::QueryGenerate() as $country){
        if($country['Command_vCountry']){
            $countries[strtolower($country['Command_vCountry'])]=1;
        }
        
        if($country['Command_vCountry'] and isset($Countries_Continent[$country['Command_vCountry']])){
            $continents[strtolower($Countries_Continent[$country['Command_vCountry']])]=1;
        }
    }
    
    $countries= array_keys($countries);
    $continents=array_keys($continents);
    
    sort($countries); 
    sort($continents); ?>
<h2>
    <img src='<?= PageIndex()?>Image/Icons/record.png' width='20px'> 
    <?php if($country_filter!='all'){ ?>
        <?= ImageCountry($country_filter, 30); ?>
        <nobr><?= ml('Records.Title.Country',CountryName($country_filter)) ?></nobr>
    <?php }elseif($continent_filter){ ?>
        <nobr><?= ml('Records.Title.Continent',$Continents[$continent_filter]) ?></nobr>
    <?php }else{ ?>
        <nobr><?= ml('Records.Title.World') ?></nobr>
    <?php } ?>
        
     <select onchange="document.location='<?= PageIndex()?>' + this.value ">
        <option <?= ($country_filter=='0' )?'selected':''?> value="Records/all/<?= $DisciplineCode?"/$DisciplineCode":""?>"><?= ml('Records.Select.WorldRecord') ?></option>
        
        
        
        <option disabled >&#9642; <?= ml('Records.Select.ContinentsRecord') ?></option>
        <?php 
        foreach($Continents as $Code=>$Continent){
            if(in_array($Code,$continents)){ ?>    
            <option <?= $continent_filter== $Code?'selected':''?>  value="Records/_<?= $Code ?><?= $DisciplineCode?"/$DisciplineCode":""?>">        
                <?= $Continent ?>
            </option> 
        <?php }        
            if(!in_array($Code,$continents) and $continent_filter==$Code){ ?>
                <option selected><?= $Continent ?> - <?= ml('Records.Select.ResultNotFound') ?></option>
            <?php }
        } ?>        
        <option disabled >&#9642; <?= ml('Records.Select.NationalsRecord') ?></option>
        <?php if(!in_array($country_filter,$countries) and $country_filter!='all'){ ?>
            <option selected><?= CountryName($country_filter) ?> [<?= strtoupper($country_filter) ?>] - <?= ml('Records.Select.ResultNotFound') ?></option>
        <?php } ?>            
        <?php foreach($countries as $country){ ?>
                <option <?= $country_filter==$country?'selected':''?> value="Records/<?= $country?><?= $DisciplineCode?"/$DisciplineCode":""?>">        
                    <?= CountryName($country) ?> [<?= strtoupper($country) ?>]
                </option> 
        <?php } ?>      
    </select>   
    <?php if($DisciplineCode){ ?>
        <br>
        <?= ImageEvent($DiscipineFilter['Discipline_CodeScript'],50) ?> 
        <?=$DiscipineFilter['Discipline_Name']?>
    <?php } ?>            
</h2>
<?php if($DiscipineFilter['Discipline_Status']=='Archive'){ ?>
<h2 class="error">
    <?=  ml('Event.Archive.Title') ?>
</h2>   
<?php } ?>
<?php if($DisciplineCode){ ?>
    <?= EventBlockLinks($DiscipineFilter,'records'); ?>
<?php } ?>
<?php if(sizeof($results)){ ?>
<table class="Records">
                <tr class="tr_title">
                <td><?= ml('Records.Table.Date') ?></td>
                <?php if(!$DisciplineCode){ ?>
                    <td><?= ml('Records.Table.Event') ?></td>
                <?php } ?>
                <td><?= ml('Records.Table.Single') ?></td>
                <td><?= ml('Records.Table.Average') ?></td>
                <td><?= ml('Records.Table.Competitor') ?></td>
                <td><?= ml('Records.Table.Competition') ?></td>
            </tr>
<?php $record_out=array();
    foreach($results as $date=>$comp){
        foreach($comp as $ci=>$c)if(strpos($c['Competition_WCA'],'t.')===false){ ?>
            <tr>
                <td>
                    <?= date_range($c['Competition_EndDate'],$c['Competition_EndDate']); ?>
                </td> 
                <?php if(!$DisciplineCode){ ?>
                <td>
                    <?= ImageEvent($c['Discipline_CodeScript'],25,$c['Discipline_Name']); ?> 
                    <a href="<?= LinkDiscipline($c['Discipline_Code']) ?>">
                        <?= $c['Discipline_Name'] ?>
                    </a>
                </td>
                <?php } ?>

                <?php $class="";
                    if(!in_array($c['Discipline_ID'].'_'.$c['Attempt_Special'],$record_out)){
                        $record_out[]=$c['Discipline_ID'].'_'.$c['Attempt_Special'];
                        $class="message";
                    } ?>   
                <td class="attempt border-left-solid border-right-solid">
                    <?php if(in_array($c['Attempt_Special'],array('Best','Sum'))){ ?>
                         <span class="<?= $class ?>"><?= $c['Attempt_vOut'] ?></span>
                     <?php } ?>
                </td>

                <td class="attempt border-left-solid border-right-solid">
                    <?php if(!in_array($c['Attempt_Special'],array('Best','Sum'))){ ?>
                         <span class="<?= $class ?>"><?= $c['Attempt_vOut'] ?></span>
                     <?php } ?>
                </td>
                <td>
                    <?php 
                    DataBaseClass::FromTable("Command","ID=".$c['Command_ID']);
                    DataBaseClass::Join_current("CommandCompetitor");
                    DataBaseClass::Join_current("Competitor");
                    DataBaseClass::OrderClear("Competitor","Name");
                    $competitors=DataBaseClass::QueryGenerate();
                    foreach($competitors as $competitor){ ?>
                        <p>
                            <img width="25" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competitor['Competitor_Country'])?>.png">
                            <a href="<?= PageIndex() ?>Competitor/<?= $competitor['Competitor_WCAID']?$competitor['Competitor_WCAID']:$competitor['Competitor_ID'] ?>"><?= trim(explode("(",$competitor['Competitor_Name'])[0]) ?></a></p>
                    <?php } ?>

                </td>
                <td>
                    <img width="25" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($c['Competition_Country'])?>.png">
                    <a href="<?= LinkCompetition($c['Competition_WCA']) ?>">
                        <?= $c['Competition_Name'] ?>
                    </a>
                </td>
                <?php if($c['Command_Video']){ ?>    
                    <td>
                        <a target=_blank" href="<?= $c['Command_Video'] ?>"><img class="video"  src="<?= PageIndex()?>Image/Icons/Video.png"></a>
                    </td>    
                <?php } ?>
            </tr>
        <?php }
    } ?>
    </table>
<?php }else{ ?>
<h3><?= ml('Records.NotFound') ?></h3>
<?php } ?>
 </div>
 
<?= mlb('Records.NotFound'); ?>
<?= mlb('Records.Select.ResultNotFound') ?>
<?= mlb('Records.Title.Country') ?>
<?= mlb('Records.Title.Continent') ?>