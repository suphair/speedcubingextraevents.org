<?php includePage('Navigator'); ?>
<?php $Language=$_SESSION['language_select'];?>
<h2><img src='<?= PageIndex()?>Image/Icons/regulation.png' width='20px'> <?= ml('Regulations.Description'); ?></h2>
<h3><img  src="<?= PageIndex()?>Logo/Logo_Color.png" width="20px"> <?= ml('Regulations.LinkRegulationSEE', PageIndex().'MainRegulations'); ?>  <?php if(CheckAccess('MainRegulations.Edit')){ ?> ▪ <a  target="_blank" href="<?= ml('Regulations.LinkRegulationSEE.Edit',false)?>"><?= ml('*.Edit')?></a><?php } ?></h3>
<h3><img  src="<?= PageIndex()?>Image/Icons/WCA.png" width="20px"> <?= ml('Regulations.LinkRegulationWCA'); ?></h3>

<?php 
$discipline_default=[];
$language_default=[];
DataBaseClass::Query("Select D.ID, D.Name, D.Code,R.Text,R.Language from Discipline D  "
        . " left outer join Regulation R on R.Event=D.ID where D.Status='Active' and R.Text!='' order by R.ID");
foreach(DataBaseClass::getRows() as $row){
    $discipline_default[$row['ID']]=$row['Text'];
    $language_default[$row['ID']]=$row['Language'];
} ?>

<?php DataBaseClass::Query("Select D.ID, D.Name, D.Code,D.CodeScript,R.Text from Discipline D  "
        . " left outer join Regulation R on R.Event=D.ID and R.Language='$Language' where D.Status='Active'");
$disciplines=DataBaseClass::getRows(); ?>
<div class="regulation line">
    <?php foreach($disciplines as $discipline_row){ ?>
        <a href="#<?= $discipline_row['Code'] ?>"><?= ImageDiscipline($discipline_row['CodeScript'],35) ?></a> 
    <?php } ?>
</div>
<hr class="hr_round">
<?php foreach($disciplines as $discipline_row){ 
    $other_language=false;
    if(!$discipline_row['Text'] and isset($discipline_default[$discipline_row['ID']])){
        $discipline_row['Text']=$discipline_default[$discipline_row['ID']];
        $other_language=$language_default[$discipline_row['ID']];
    }
    ?>
<a name="<?= $discipline_row['Code'] ?>"></a>
<div class="form">
   <h2>
        <?= ImageDiscipline($discipline_row['CodeScript'],40) ?>
        <?= $discipline_row['Name'] ?> 
       <?php if($other_language){ ?>
            <?= ImageCountry($other_language,40); ?>
        <?php } ?>
   </h2>
    <div id="Text_<?= $discipline_row['ID'] ?>">
        <?= Echo_format($discipline_row['Text']);?>
    </div>
    <?= EventBlockLinks(['Discipline_Code'=>$discipline_row['Code'] ,'Discipline_ID'=>$discipline_row['ID'],'Discipline_Status'=>'Active'],'regulations'); ?>
</div>
<br>
<?php 
} ?>

<?= mlb('Regulations.LinkRegulationSEE.Link')?>
<?= mlb('Regulations.LinkRegulationSEE.Edit')?>

