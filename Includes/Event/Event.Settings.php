    <?php
$request=Request();
$ID=0;
$Language=getLanguages()[0];
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

DataBaseClass::FromTable('Discipline'); 
DataBaseClass::OrderClear('Discipline','Status'); 
DataBaseClass::Order_current('Name'); 
$disciplines=DataBaseClass::QueryGenerate();
?>
<h1 class="<?= $discipline['Discipline_Status'] ?>"><?= $discipline['Discipline_Name'] ?> / Settings</h1>

<table width="100%"><tr><td width="10%" style='border-right: 1px solid #333'>
<table class="table_info" style="white-space: nowrap">
    <?php foreach($disciplines as $d=>$discipline_row){ ?>
        <tr>
            <td><?= ImageEvent($discipline_row['Discipline_CodeScript'],1) ?></td>
            <td>
                <a class="<?= $discipline['Discipline_Code'] ==$discipline_row['Discipline_Code']?'list_select':''?>" href="<?= PageIndex()?>/Event/<?= $discipline_row['Discipline_Code'] ?>/Settings"><?= $discipline_row['Discipline_Name'] ?></a>
                <?php if($discipline_row['Discipline_Status']!='Active'){ ?>
                   <i class="fas fa-ban"></i>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>     
</td><td width="90%" style='padding-left: 10px'>            
<table class="table_info">
    <tr>
        <td>Image</td>
        <td><?= ImageEvent($discipline['Discipline_CodeScript'],2) ?></td>    
    </tr>    
    <form method="POST" action="<?= PageAction("Event.Edit") ?>">
    <input name="ID" type="hidden" value="<?=  $discipline['Discipline_ID'] ?>" />
    <tr>
        <td>Name</td>
        <td><input type="text" name="Name" value="<?= $discipline['Discipline_Name'] ?>" /></td>   
    </tr>    
    <tr>
        <td>Code</td>
        <td><input type="text" name="Code" value="<?= $discipline['Discipline_Code'] ?>" /></td>   
    </tr> 
    <tr>
        <td>Development code</td>
        <td><i class="far fa-file-code"></i> <?=  $discipline['Discipline_CodeScript'] ?></td>   
    </tr> 
    <tr>
        <td>Simple</td>
        <td><input  type="checkbox" name="Simple" <?= $discipline['Discipline_Simple']?'checked':'' ?> ></td>   
    </tr> 
    <tr>
        <td>Inspection</td>
        <td>
            <input  type="radio" name="Inspection" value="15" <?= $discipline['Discipline_Inspection']==15?'checked':'' ?>>15 seconds&nbsp;
            <input  type="radio" name="Inspection" value="20" <?= $discipline['Discipline_Inspection']==20?'checked':'' ?>>20 seconds  
        </td>   
    </tr>   
    <tr>
        <td>Team</td>
        <td>
            <?php for($i=1;$i<=4;$i++){ ?>
                <input type="radio" name="Competitors" <?= $discipline['Discipline_Competitors']==$i?'checked':''?> value="<?= $i ?>"><?= $i ?>&nbsp;
            <?php } ?>
        </td>   
    </tr>
    
    <?php DataBaseClass::Query(' Select '
                . ' F.ID,F.Result, F.Attemption,max(DF.ID) DF, max(E.ID) E '
                . ' from Format F '
                . ' left outer join DisciplineFormat DF on DF.Format=F.ID and DF.Discipline='.$discipline['Discipline_ID']. ' ' 
                . ' left outer join Event E on E.DisciplineFormat=DF.ID '
                . ' group by F.ID,F.Result, F.Attemption '
                . ' order by F.Result, F.Attemption'); 
        $format_result_disabled=false;
        foreach(DataBaseClass::getRows() as $f=>$format){ 
            if($format['E']){$format_result_disabled=true;} ?>
    <tr>
        <td><?php if(!$f) {?>Formats<?php } ?></td>
        <td>
            <input  type="checkbox" <?= $format['E']?'disabled':'' ?> <?= $format['DF']?'checked':'' ?> name="Formats[]" value="<?= $format['ID'] ?>" > <?= $format['Result']." of ".$format['Attemption'] ?>
            <?php if($format['E']){?>
                <i class="fas fa-info-circle"></i> competitions exist
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
    
    <tr>
        <td>Format results</td>
        <td>
            <?php if($format_result_disabled){  ?>
                <input hidden name="FormatResult" value="<?= $discipline['FormatResult_ID'] ?>">
                <?php DataBaseClass::Query(' Select * from FormatResult order by ID');
                foreach(DataBaseClass::getRows() as $format_result)
                    if($discipline['FormatResult_ID']==$format_result['ID']){?>
                        <?= $format_result['Name']?>
                    <?php } ?>
            <?php }else{ ?>
                <select name="FormatResult">
                <?php DataBaseClass::Query(' Select * from FormatResult order by ID');
                foreach(DataBaseClass::getRows() as $format_result){ ?>
                    <option value="<?= $format_result['ID']?>" <?= $discipline['FormatResult_ID']==$format_result['ID']?'selected':''; ?>><?= $format_result['Name']?></option>
                <?php } ?>
                </select>
            <?php } ?>
        </td>
    </tr>
    <?php $TNoodles=["222","333","333oh","333bf","444","555","skewb","pyram","sq1","clock","minx","666","777"] ?>
    <tr>
        <td>TNoodle event</td>
        <td>
            <select name="TNoodle">
            <option value=""></option>
            <?php foreach( $TNoodles as $i=>$code){?>
               <option <?= $discipline['Discipline_TNoodle']==$code?'selected':'' ?> value="<?= $code ?>"><?= $code ?></option>
            <?php } ?>
            </select>   
        </td>    
    </tr>
    <tr>
        <td>Use TNoodle's picture</td>
        <td><input  type="checkbox" name="GlueScrambles" <?= $discipline['Discipline_GlueScrambles']?'checked':'' ?> ></td>
    </tr>  
    <tr>
        <td>Cut scrambles</td>
        <td><input  type="checkbox" name="CutScrambles" <?= $discipline['Discipline_CutScrambles']?'checked':'' ?> ></td>
    </tr>  
    <tr>
        <?php $Discipline_TNoodles=explode(",",$discipline['Discipline_TNoodles']); ?>
        <td>Glue the scrambles</td>
        <td>
            <?php foreach( $TNoodles as $i=>$code){?>
            <input type="checkbox" name="TNoodles[<?= $code?>]" <?= in_array($code,$Discipline_TNoodles)?"checked":"" ?> /><?= $code?>&nbsp;
            <?php if($i==5){ ?>
                    <br>
               <?php } ?>
            <?php } ?>   
        </td>
    <tr>   
    <tr>
        <td>Multiplier</td>
        <td><input type="number" min="1" max="10" required="" name="TNoodlesMult" value="<?= $discipline['Discipline_TNoodlesMult'] ?>"></td>
    </tr>    
    <?php 
    $comments=json_decode($discipline['Discipline_Comment'],true);
    foreach(getLanguages() as $language){  
        if(!isset($comments[$language] )){
            $comments[$language] ="";
        } ?>
    <tr>
        <td>Information for delegates <?= ImageCountry($language)?></td>
        <td><input name="Comment[<?= $language ?>]" style="width: 300px" value='<?= $comments[$language] ?>'></td>
    </tr>
    <?php } ?>
    <tr>
        <td>Information for scrambles</td>
        <td><i class="fas fa-info-circle"></i> max 5 rows, maximum of 45 characters per line</td>
    </tr>    
    <tr>
        <td/>
        <td><textarea name="ScrambleComment" style="width: 240px;height:65px;"><?= $discipline['Discipline_ScrambleComment']; ?></textarea></td>
    </tr>    
    <tr>
        <td/>
        <td><button><i class="fas fa-save"></i> Save</button></td>
    </tr>
    <tr>
        <td><hr></td>
        <td><hr></td>
    </tr>
    </form>
    <tr>
        <td>Delete event</td>
        <td>
            <?php  DataBaseClass::FromTable("Event");
            DataBaseClass::Join_current("DisciplineFormat");
            DataBaseClass::Join_current("Discipline");
            DataBaseClass::Where_current("ID='".$discipline['Discipline_ID']."'");
            $competition=DataBaseClass::QueryGenerate(); ?>
            <?php if (DataBaseClass::rowsCount()==0){ ?>
                <form method="POST" action="<?= PageAction("Event.Delete") ?>"   onsubmit="return confirm('Attention: Confirm the deletion.')">
                    <input name="ID" type="hidden" value="<?= $discipline['Discipline_ID'] ?>" />
                    <button class="delete"><i class="fas fa-trash-alt"></i> Delete</button>
                </form>
            <?php }else{ ?>
                 <i class="fas fa-info-circle"></i> can't be deleted because attempts exist
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php if($discipline['Discipline_Status']=='Active'){ ?>
                <i class="fas fa-check-circle"></i> The event is active
            <?php }else{ ?>
                <i class="fas fa-ban"></i> Event in the archive
            <?php } ?>
        </td>
        <td>
            <?php if($discipline['Discipline_Status']=='Active'){ ?>
              <form method="POST" action="<?= PageAction("Event.Archive") ?>"  onsubmit="return confirm('Confirm archive')">
                  <input name="ID" type="hidden" value="<?= $discipline['Discipline_ID'] ?>" />
                  <button class="delete"><i class="fas fa-ban"></i> Send to archive</button>
              </form>
          <?php }else{ ?>
              <form method="POST" action="<?= PageAction("Event.Active") ?>"   onsubmit="return confirm('Confirm return')">
                  <input name="ID" type="hidden" value="<?= $discipline['Discipline_ID'] ?>" />
                  <button><i class="fas fa-undo-alt"></i> To return from the archive</button>
              </form>
          <?php } ?>
    </tr>    
    <tr>
        <td><hr></td>
        <td><hr></td>
    </tr>
<form method="POST" action="<?= PageAction('Regulation.Edit')?>">
    <input hidden name='ID' value='<?= $discipline['Discipline_ID'] ?>'>
    <?php foreach(getLanguages() as $language){ 
        DataBaseClass::FromTable('Regulation');
        DataBaseClass::Where_current('Event='.$discipline['Discipline_ID']);
        DataBaseClass::Where_current("Language='$language'");        
        $result=DataBaseClass::QueryGenerate(false);
        if(isset($result['Regulation_Text'])){
            $Regulation=$result['Regulation_Text'];
        }else{
            $Regulation='';
        } ?>
    <tr>
        <td>Regulations <?= ImageCountry($language)?></td>
        <td><textarea class="big_data" name="regulation[<?=$language ?>]"><?= $Regulation ?></textarea></td>
     <tr>   
    <?php } ?>
    <tr>
        <td></td>
        <td><button><i class="fas fa-save"></i> Save regulations</button></td>
    </tr>
    <tr>
        <td><hr></td>
        <td><hr></td>
    </tr>
    <?= EventBlockLinks($discipline,'settings',true); ?>    
</form>
</table>    
    
</td></tr></table>


    