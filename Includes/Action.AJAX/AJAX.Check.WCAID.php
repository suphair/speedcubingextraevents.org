<?php
if(!isset($_GET['WCAID']) or !$_GET['Competitor'] or !is_numeric($_GET['Competitor'])){
    exit();
}

$Competitor=$_GET['Competitor'];
DataBaseClass::FromTable('Command');
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Where_current("Competitor='$Competitor'");
DataBaseClass::Join('Command','Event');
DataBaseClass::Join_current('Competition');
$row=DataBaseClass::QueryGenerate(false);


if(!isset($row['Competition_ID']) or ! CheckAccess('Competition.Event.Settings', $row['Competition_ID'])){
    exit();
} ?>

<input ID="WCAIDsearch<?= $Competitor ?>" type="hidden" value="<?= $_GET['WCAID'] ?>" />
<?php
DataBaseClass::FromTable('Competitor',"WCAID='{$_GET['WCAID']}'");
$row=DataBaseClass::QueryGenerate(false);
if(isset($row['Competitor_ID'])){ ?>
    <?= $row['Competitor_Name'] ?> <?= ImageCountry($row['Competitor_Country'], 20); ?> 
    <input class="form_row" type="submit" value="<?= ml('*.Link',false); ?>" style="margin:0px; padding:1px 2px;"
     onclick="return confirm('Attention: Link with <?= $_GET['WCAID'] ?>?')">
    <?php exit();
}

DataBaseClassWCA::Query(" Select P.*,C.iso2 from Persons P join Countries C on C.id=P.countryId where P.id='{$_GET['WCAID']}'");
$row=DataBaseClassWCA::getRow();
if(isset($row['id'])){ ?>
    <?php DataBaseClass::Query("insert into Competitor (Name,WCAID,Country) values ('".Short_Name($row['name'])."','{$_GET['WCAID']}','{$row['iso2']}')")?>
    <?= $row['name'] ?> <?= ImageCountry($row['iso2'], 20); ?> 
    <input class="form_row" type="submit" value="<?= ml('*.Link',false); ?>" style="margin:0px; padding:1px 2px;"
    onclick="return confirm('Attention: Link with <?= $_GET['WCAID'] ?>?')">
    <?php exit();
}

$url=GetIni('WCA_API', 'person').'/'.$_GET['WCAID'];
$person= json_decode(file_get_contents_curl($url)); ?>
<?php if(!isset($person->person)){ ?>
    <a target="_blank" href="<?= $url ?>" class="error">{<?= strtoupper($_GET['WCAID']) ?>} not found on the WCA</a>
    <?php exit(); ?>
<?php } ?>
<?= $person->person->name; ?> <?= ImageCountry($person->person->country_iso2, 20); ?> 
<?php CompetitorReplace($person->person); ?>    
<input class="form_row" type="submit" value="<?= ml('*.Link',false); ?>" style="margin:0px; padding:1px 2px;"
       onclick="return confirm('Attention: Link with <?= $_GET['WCAID'] ?>?')">
<?php exit(); ?>