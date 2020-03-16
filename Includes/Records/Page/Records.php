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
        DataBaseClass::Where_current("WCA not like 't.%'");
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

<h1>
    <?php if($DisciplineCode){ ?>
        <?= ImageEvent($DiscipineFilter['Discipline_CodeScript'],50) ?> 
        <?=$DiscipineFilter['Discipline_Name']?> /
    <?php } ?>            
     <?= ml('Records.Records') ?>   
</h1>
<?php if($DiscipineFilter['Discipline_Status']=='Archive'){ ?>
    <h2>
        <i class="fas fa-angle-double-right"></i> <?=  ml('Event.Archive.Title') ?>
    </h2>   
<?php } ?>
<table width="100%"><tr><td>
    <table class="table_info">
        <tr>
            <td><?= ml('Records.Show') ?></td>
            <td><i class="far fa-check-square"></i> <?= ml('Records.ShowHistory') ?> </td>
        </tr
        <tr>
            <td><?= ml('Records.Region') ?></td>
            <td>
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
                                <?= CountryName($country) ?>
                            </option> 
                    <?php } ?>      
                </select>
            </td>
        </tr>    
        <tr>
            <td><?= ml('Records.ExtraEvent') ?></td>
            <td>
                <select onchange="document.location='<?= PageIndex()?>' + this.value ">
                    <option value="Records/<?= $country_filter ?>">All events</option>
                <?php foreach($disciplines as $discipline_row){ ?>   
                    <option value="Records/<?= $country_filter ?>/<?= $discipline_row['Discipline_Code'] ?>" <?= strtolower($discipline_row['Discipline_Code'])==$DisciplineCode?'selected':''?> >
                        <?= $discipline_row['Discipline_Name'] ?>
                    </option>
                <?php } ?>    
                </select>                
            </td>
        </tr>    
    </table>
</td><td>
<?php if($DisciplineCode){ ?>
    <?= EventBlockLinks($DiscipineFilter,'records'); ?>
<?php } ?>
</td></tr></table>
<h2>
  
    <?php if($country_filter=='all' and $continent_filter=='') { ?>
        <?= ml('Event.Country.Title.All'); ?>
    <?php }elseif($country_filter!='all'){ ?>
        <?= CountryName($country_filter); ?>  
    <?php }else{ ?>
        <?= $Continents[$continent_filter]?>
    <?php } ?>    
</h2>   

<?php if(sizeof($results)){ ?>
<table class="table_new">
    <thead>
                <tr >
                <td><?= ml('Records.Table.Date') ?></td>
                <td><?= ml('Records.ExtraEvent') ?></td>
                <td class="table_new_right"><?= ml('Records.Table.Single') ?></td>
                <td class="table_new_right"><?= ml('Records.Table.Average') ?></td>
                <td><?= ml('Records.Table.Competitor') ?></td>
                <td><?= ml('Records.Table.Country') ?></td>
                <td><?= ml('Records.Table.Competition') ?></td>
                <td></td>
            </tr>
    </thead>
    <tbody>
<?php $record_out=array();
    foreach($results as $date=>$comp){
        foreach($comp as $ci=>$c)if(strpos($c['Competition_WCA'],'t.')===false){ ?>
            <tr>
                <td>
                    <?= date_range($c['Competition_EndDate'],$c['Competition_EndDate']); ?>
                </td> 
                <td>
                    <?= ImageEvent($c['Discipline_CodeScript'],25,$c['Discipline_Name']); ?> 
                    <a href="<?= LinkDiscipline($c['Discipline_Code']) ?>">
                        <?= $c['Discipline_Name'] ?>
                    </a>
                </td>

                <?php $class="";
                    if(!in_array($c['Discipline_ID'].'_'.$c['Attempt_Special'],$record_out)){
                        $record_out[]=$c['Discipline_ID'].'_'.$c['Attempt_Special'];
                        $class="table_new_PB";
                    } ?>   
                <td class="<?= $class ?> table_new_right table_new_bold">
                    <?php if(in_array($c['Attempt_Special'],array('Best','Sum'))){ ?>
                        <?= $c['Attempt_vOut'] ?>
                     <?php } ?>
                </td>

                <td class="<?= $class ?> table_new_right table_new_bold">
                    <?php if(!in_array($c['Attempt_Special'],array('Best','Sum'))){ ?>
                        <?= $c['Attempt_vOut'] ?>
                     <?php } ?>
                </td>
                    <?php  
                    DataBaseClass::FromTable("Command","ID=".$c['Command_ID']);
                    DataBaseClass::Join_current("CommandCompetitor");
                    DataBaseClass::Join_current("Competitor");
                    DataBaseClass::OrderClear("Competitor","Name");
                    $competitors=DataBaseClass::QueryGenerate(); ?>
                <td>
                    <?php foreach($competitors as $competitor){ ?>
                        <p>
                            <a href="<?= PageIndex() ?>Competitor/<?= $competitor['Competitor_WCAID']?$competitor['Competitor_WCAID']:$competitor['Competitor_ID'] ?>"><?= trim(explode("(",$competitor['Competitor_Name'])[0]) ?></a>
                        </p>
                    <?php } ?>
                </td>
                <td>
                    <?php foreach($competitors as $competitor){ ?>
                        <p>
                            <?= ImageCountry($competitor['Competitor_Country'])?>
                            <?= CountryName($competitor['Competitor_Country'])?>
                        </p>
                    <?php } ?>
                </td>
                <td>
                    <?= ImageCountry($c['Competition_Country'])?>
                    <a href="<?= LinkCompetition($c['Competition_WCA']) ?>">
                        <?= $c['Competition_Name'] ?>
                    </a>
                </td>
                <td>
                <?php if($c['Command_Video']){ ?>            
                    <a target=_blank" href="<?= $c['Command_Video'] ?>"><i class="fas fa-video"></i></a>
                <?php } ?>
                </td>    
            </tr>
        <?php }
    } ?>
    </tbody>
</table>
<?php }else{ ?>
<i class="fas fa-exclamation-circle"></i> <?= ml('Records.NotFound') ?>
<?php } ?>
 </div>