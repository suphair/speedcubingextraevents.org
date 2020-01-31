<?php includePage('Navigator'); ?>
<?php $Language=$_SESSION['language_select'];?>
<h2><img src='<?= PageIndex()?>Image/Icons/regulation.png' width='20px'> <?= ml('Regulations.Description'); ?></h2>
  
<h3><?= ml('Regulations.LinkRegulationSEE', PageIndex().'MainRegulations'); ?>
  ▪ <?= ml('Regulations.LinkRegulationWCA'); ?></h3>

<?php 
$discipline_default=[];
$language_default=[];
DataBaseClass::Query("Select D.ID, D.Name, D.Code,R.Text,R.Language from Discipline D  "
        . " left outer join Regulation R on R.Event=D.ID where D.Status='Active' and R.Text!='' order by R.ID");
foreach(DataBaseClass::getRows() as $row){
    $discipline_default[$row['ID']]=$row['Text'];
    $language_default[$row['ID']]=$row['Language'];
} ?>

<?php DataBaseClass::Query("Select D.Comment,D.ID, D.Name, D.Code,D.CodeScript,R.Text,D.Inspection,D.Competitors,D.TNoodles from Discipline D  "
        . " left outer join Regulation R on R.Event=D.ID and R.Language='$Language' where D.Status='Active'");
$disciplines=DataBaseClass::getRows(); ?>
<div class="regulation line">
    <?php foreach($disciplines as $discipline_row){ ?>
        <a href="#<?= $discipline_row['Code'] ?>"><?= ImageEvent($discipline_row['CodeScript'],25) ?></a> 
    <?php } ?>
</div>
<hr>
<?php 
if(CheckAccess('Event.Settings')){
DataBaseClass::Query("Select D.* from Discipline D"
                         . " Left outer join Regulation R on D.ID=R.Event"
                         . " where D.Status='Active'  and R.ID is null"); 
$eventswithoutregulations=DataBaseClass::getRows();
if(sizeof($eventswithoutregulations)){ ?>
    <div class="form">
        <h2 class="error"><?= ml('Competitor.Delegate.Regulations') ?></h2>
    <?php foreach($eventswithoutregulations as $eventwithoutregulations){ ?>
    <div class="border_warning"><a href="<?= PageIndex()?>/Regulations/#<?= $eventwithoutregulations['Code']?>"><?= $eventwithoutregulations['Name']?></a></div>
    <?php } ?>
    </div>
<?php } 
} ?>



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
        <?= ImageEvent($discipline_row['CodeScript'],40) ?>
        <?= $discipline_row['Name'] ?> 
       <?php if($other_language){ ?>
            <?= ImageCountry($other_language,40); ?>
        <?php } ?>
   </h2>
    <div id="Text_<?= $discipline_row['ID'] ?>">
        <?php $Text='';
        if($discipline_row['Competitors']>1){
            $Text.=Parsedown(str_replace("%1",$discipline_row['Competitors'],GetBlockText('Regulation.Competitors',$Language)));
        }
        if($discipline_row['Text']){
            $Text.=Parsedown($discipline_row['Text']);
        }else{
            $Text.="<div class=border_warning>".ml('Regulation.Writing')."</div>";
        }
        if($discipline_row['Inspection']==20){
            $Text.=Parsedown(GetBlockText('Regulation.Inspect.20',$Language));
        }
        if(strpos($discipline_row['CodeScript'],'mguild')!==false){
            $Text.=Parsedown(GetBlockText('Regulation.mguild',$Language));
        }
        
        if($discipline_row['TNoodles']){
            $Text.=Parsedown(GetBlockText('Regulation.puzzles',$Language));
        } 
        ?>
            <?= $Text;?>
    </div>
    <?= EventBlockLinks(['Discipline_CodeScript'=>$discipline_row['CodeScript'] ,'Discipline_Code'=>$discipline_row['Code'] ,'Discipline_ID'=>$discipline_row['ID'],'Discipline_Status'=>'Active'],'regulations'); ?>
</div>
<br>
<?php 
} ?>

<?= mlb('Regulations.LinkRegulationSEE.Link')?>
<?= mlb('Regulations.LinkRegulationSEE.Edit')?>

