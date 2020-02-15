<?php
if(!isset($_GET['WCAID']) or !$_GET['Competitor'] or !is_numeric($_GET['Competitor'])){
    exit();
}
$WCAID= strtoupper(DataBaseClass::Escape($_GET['WCAID']));
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

<input ID="WCAIDsearch<?= $Competitor ?>" type="hidden" value="<?= $WCAID ?>" />
<?php
DataBaseClass::FromTable('Competitor',"WCAID='$WCAID'");
$row=DataBaseClass::QueryGenerate(false);
if(isset($row['Competitor_ID'])){ ?>
    <?= $row['Competitor_Name'] ?> / <?= CountryName($row['Competitor_Country']); ?> 
    <button onclick="return confirm('Attention: Link with <?= $WCAID ?>?')"><i class="fas fa-link"></i> Link</button>
    <?php exit();
}

DataBaseClassWCA::Query(" Select P.*,C.iso2 from Persons P join Countries C on C.id=P.countryId where P.id='$WCAID'");
$row=DataBaseClassWCA::getRow();
if(isset($row['id'])){ ?>
    <?php DataBaseClass::Query("insert into Competitor (Name,WCAID,Country) values ('".Short_Name($row['name'])."','$WCAID','{$row['iso2']}')")?>
    <?= $row['name'] ?> / <?= CountryName($row['iso2']); ?> 
    <button onclick="return confirm('Attention: Link with <?= $WCAID ?>?')"><i class="fas fa-link"></i> Link</button>
    <?php exit();
}

$url=GetIni('WCA_API', 'person').'/'.$WCAID;
$person= json_decode(file_get_contents_curl($url)); ?>
<?php if(!isset($person->person)){ ?>
    <i class="fas fa-exclamation-triangle"></i> <a target="_blank" href="<?= $url ?>">{<?= strtoupper($WCAID) ?>} not found on the WCA</a>
    <?php exit(); ?>
<?php } ?>
<?= $person->person->name; ?> / <?= CountryName($person->person->country_iso2); ?> 
<?php CompetitorReplace($person->person); ?>        
<button onclick="return confirm('Attention: Link with <?= $WCAID ?>?')"><i class="fas fa-link"></i> Link</button>
<?php exit(); ?>