<?php
if(!isset($_GET['WCAID']) or !$_GET['Competition'] or !is_numeric($_GET['Competition'])){
    exit();
}
$WCAID= strtoupper(DataBaseClass::Escape($_GET['WCAID']));
DataBaseClass::FromTable('Competition',"ID=".$_GET['Competition']);
$row=DataBaseClass::QueryGenerate(false);

if(!isset($row['Competition_ID']) or !CheckAccess('Competition.Event.Settings', $row['Competition_ID'])){
    exit();
} ?>

<input ID="WCAIDsearch" type="hidden" value="<?= $WCAID ?>" />

<?php
DataBaseClass::Query("Select R.ID,C.Name from Registration R join Competitor C on C.ID=R.Competitor where R.Competition=".$row['Competition_ID']." and C.WCAID='".$WCAID."'");
$row=DataBaseClass::getRow(false);
if(isset($row['ID'])){ ?>
    <span style="color:red">The competitor {<?= $row['Name'] ?>} is already registered</span>
    <?php exit();
}
?>
<?php
DataBaseClass::FromTable('Competitor',"WCAID='$WCAID'");
$row=DataBaseClass::QueryGenerate(false);
$needAdd=true;
if(isset($row['Competitor_ID'])){
    $needAdd=false;
}

DataBaseClassWCA::Query(" Select P.*,C.iso2 from Persons P join Countries C on C.id=P.countryId where P.id='$WCAID'");
$row=DataBaseClassWCA::getRow();
if(isset($row['id'])){ ?>
    <?php 
    if($needAdd){
        DataBaseClass::Query("insert into Competitor (Name,WCAID,Country) values ('".Short_Name($row['name'])."','$WCAID','{$row['iso2']}')");
    } ?>
    <?= $row['name'] ?> <?= ImageCountry($row['iso2'], 20); ?> 
    <input class="form_row" type="submit" value="Add registration" style="background-color:lightgreen;"
    onclick="return confirm('Attention: Add competitor  <?= $WCAID ?>?')">
    <?php exit();
}

$url=GetIni('WCA_API', 'person').'/'.$WCAID;
$person= json_decode(file_get_contents_curl($url)); ?>
<?php if(!isset($person->person)){ ?>
    <a target="_blank" href="<?= $url ?>" class="error">{<?= $WCAID ?>} not found on the WCA</a>
    <?php exit(); ?>
<?php } ?>
<?= $person->person->name; ?> <?= ImageCountry($person->person->country_iso2, 20); ?> 
<?php CompetitorReplace($person->person); ?>    
<input class="form_row" type="submit" value="Add registration" style="background-color:lightgreen;"
       onclick="return confirm('Attention: Add competitor <?= $WCAID ?>?')">
<?php exit(); ?>