<?php includePage('Navigator'); ?>
    <?php
$request=Request();
$ID=0;

if(isset($request[1])){
    $Code=DataBaseClass::Escape($request[1]);
    DataBaseClass::FromTable('Discipline', "Code='$Code'");
    DataBaseClass::Join_current('FormatResult');
    $discipline=DataBaseClass::QueryGenerate(false);
    if(isset($discipline['Discipline_ID'])){
       $ID=$discipline['Discipline_ID'];
    } 
    DataBaseClass::Join('Discipline','DisciplineFormat');
    DataBaseClass::Join_current('Format');
    DataBaseClass::OrderClear('Format',' Result');
    DataBaseClass::OrderClear('Format',' Attemption');
   
    $formats=DataBaseClass::QueryGenerate();
}
?>
<?php IncludePage("Events_Line") ?>
<hr class="hr_round">
<h1 class="<?= $discipline['Discipline_Status'] ?>">
    <img style="vertical-align: middle" width="40px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?>:
    <a href="<?= PageIndex()?>Event/<?= $discipline['Discipline_Code'] ?>"><?= $discipline['Discipline_Name'] ?></a>
</h1>

<?= EventBlockLinks($discipline,'settings'); ?>

<div class="form">
    <form method="POST" action="<?= PageAction("Event.Edit") ?>">
    <input name="ID" type="hidden" value="<?=  $discipline['Discipline_ID'] ?>" />
    <div class="form_field">
        Name 
    </div>
    <div class="form_input">
        <input type="text" name="Name" value="<?= $discipline['Discipline_Name'] ?>" />
    </div>
    <div class="form_field">
        Code
    </div>
    <div class="form_input">
        <input type="text" name="Code" value="<?= $discipline['Discipline_Code']?>" />
    </div>
    <div class="form_field">
        Simple
    </div>
    <div class="form_input">
        <input  type="checkbox" name="Simple" <?= $discipline['Discipline_Simple']?'checked':'' ?> ><br>
    </div>
    <div class="form_field">
        Inspection
    </div>
    <div class="form_input">
        <input  type="radio" name="Inspection" value="15" <?= $discipline['Discipline_Inspection']==15?'checked':'' ?>>15 
        <input  type="radio" name="Inspection" value="20" <?= $discipline['Discipline_Inspection']==20?'checked':'' ?>>20<br>
    </div>
    <div class="form_field">
        Team
    </div>
    <div class="form_input">
        <?php for($i=1;$i<=4;$i++){ ?>
            <input type="radio" name="Competitors" <?= $discipline['Discipline_Competitors']==$i?'checked':''?> value="<?= $i ?>"><?= $i ?>
        <?php } ?>
    </div>
    <div class="form_field">
        Format 
    </div>
    <div class="form_input">
        <?php 
        DataBaseClass::Query(' Select '
                . ' F.ID,F.Result, F.Attemption,max(DF.ID) DF, max(E.ID) E '
                . ' from Format F '
                . ' left outer join DisciplineFormat DF on DF.Format=F.ID and DF.Discipline='.$discipline['Discipline_ID']. ' ' 
                . ' left outer join Event E on E.DisciplineFormat=DF.ID '
                . ' group by F.ID,F.Result, F.Attemption '
                . ' order by F.Result, F.Attemption'); 
        foreach(DataBaseClass::getRows() as $format){ ?>
        <input  type="checkbox" <?= $format['E']?'disabled':'' ?>  <?= $format['DF']?'checked':'' ?> name="Formats[]" value="<?= $format['ID'] ?>" ><?= $format['Result']." of ".$format['Attemption'] ?><br>
        <?php } ?>               
    </div>
    <div class="form_field">
        Format result 
    </div>
    <div class="form_input">
        <select name="FormatResult">
        <?php DataBaseClass::Query(' Select * from FormatResult order by ID');
        foreach(DataBaseClass::getRows() as $format_result){ ?>
            <option value="<?= $format_result['ID']?>" <?= $discipline['FormatResult_ID']==$format_result['ID']?'selected':''; ?>><?= $format_result['Name']?></option>
        <?php } ?>
        </select>
    </div>    
    
    <?php $TNoodles=["222","333","333oh","333bf","444","555","skewb","pyram","sq1","clock","minx","666","777"] ?>
    <div class="form_field">
        TNoodle event
    </div>
    <div class="form_input">
        <select name="TNoodle">
            <option value=""></option>
            <?php foreach( $TNoodles as $i=>$code){?>
               <option <?= $discipline['Discipline_TNoodle']==$code?'selected':'' ?> value="<?= $code ?>"><?= $code ?></option>
            <?php } ?>
        </select>    
    </div>
    <div class="form_field">
        Use TNoodle's picture
    </div>
    <div class="form_input">
        <input  type="checkbox" name="GlueScrambles" <?= $discipline['Discipline_GlueScrambles']?'checked':'' ?> ><br>
    </div>
    <div class="form_field">
        Cut scrambles
    </div>
    <div class="form_input">
        <input  type="checkbox" name="CutScrambles" <?= $discipline['Discipline_CutScrambles']?'checked':'' ?> ><br>
    </div>
    <div class="form_field">
        <br><b>Glue the scrambles</b>
    </div>
    <?php $Discipline_TNoodles=explode(",",$discipline['Discipline_TNoodles']); ?>
    <div class="form_field">
        TNoodle events <span class="badge"><?= $discipline['Discipline_TNoodles']?sizeof($Discipline_TNoodles):0 ?></span>
    </div>
    <br>
    <div class="form_input">
        <?php 
        foreach( $TNoodles as $i=>$code){?>
            <input type="checkbox" name="TNoodles[<?= $code?>]" <?= in_array($code,$Discipline_TNoodles)?"checked":"" ?> /><?= $code?>
            <?php if($i==5){ ?>
                    <br>
               <?php } ?>
        <?php } ?>
    </div>
    
    
    <div class="form_field">
        Multiplier
    </div>
    <div class="form_input">
        <input type="number" min="1" max="10" required="" name="TNoodlesMult" value="<?= $discipline['Discipline_TNoodlesMult'] ?>">
    </div>
    
    
    <div class="form_change">
        <input type="submit" value="Change">
    </div>
    </form>
</div>


<div class="form">
    <?= ImageEvent($discipline['Discipline_CodeScript']) ?> 
    <form name="LoadDisciplineImage" enctype="multipart/form-data" method="POST" action="<?= PageAction("Event.Image") ?>">           
        <div class="fileinputs">
            <input accept="image/jpeg,image/png" type="file" name="uploadfile" class="file"  onchange="document.forms['LoadDisciplineImage'].submit();"/>
            <div class="fakefile" id="fkf">
                <button class="form_change">Image</button>
            </div>
        </div>
        <input name="ID" type="hidden" value="<?= $discipline['Discipline_ID'] ?>" />
    </form>
</div>


<?php 
    DataBaseClass::FromTable("Event");
    DataBaseClass::Join_current("DisciplineFormat");
    DataBaseClass::Join_current("Discipline");
    DataBaseClass::Where_current("ID='".$discipline['Discipline_ID']."'");
    $competition=DataBaseClass::QueryGenerate(); ?>
    <div class="form">
        <?php if (DataBaseClass::rowsCount()==0){ ?>
                <form method="POST" action="<?= PageAction("Event.Delete") ?>"   onsubmit="return confirm('Attention: Confirm the deletion.')">
                    <input name="ID" type="hidden" value="<?= $discipline['Discipline_ID'] ?>" />
                    <input  class="delete" type="submit" value="Delete">
                </form>
       <?php }
       if($discipline['Discipline_Status']=='Active'){ ?>
                <form method="POST" action="<?= PageAction("Event.Archive") ?>">
                    <input name="ID" type="hidden" value="<?= $discipline['Discipline_ID'] ?>" />
                    <input class="delete" type="submit" value="Send to archive">
                </form>
        <?php }else{ ?>
                <form method="POST" action="<?= PageAction("Event.Active") ?>">
                    <input name="ID" type="hidden" value="<?= $discipline['Discipline_ID'] ?>" />
                    <input type="submit" value="To return from the archive">
                </form>
        <?php } ?> 
    </div>

<div class="form">
    <form method="POST" action="<?= PageAction('Regulation.Edit')?>">
        <div>
            Regulations
            <input hidden name='ID' value='<?= $discipline['Discipline_ID'] ?>'>
            <input type="submit" value="Save">
        </div>
        <?php foreach(getLanguages() as $language){ 
            DataBaseClass::FromTable('Regulation');
            DataBaseClass::Where_current('Event='.$discipline['Discipline_ID']);
            DataBaseClass::Where_current("Language='$language'");        
            $result=DataBaseClass::QueryGenerate(false);
            if(isset($result['Regulation_Text'])){
                $Regulation=$result['Regulation_Text'];
            }else{
                $Regulation='';
            }
            ?>
            <div class="form_input form_input_left">        
                <?= ImageCountry($language, 30)?>
                <b><?= CountryName($language,true)?></b><br>
                <textarea class="regulation" name="regulation[<?=$language ?>]"><?= $Regulation ?></textarea>
            </div>
        <?php } ?>
    </form>
</div>

<div class="block_comment">For scripts: <?=  $discipline['Discipline_CodeScript'] ?></div>