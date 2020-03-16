<?php
$Request= Request();
$Settings=(isset($Request[2]) and $Request[2]=='settings');
$Event_line = ObjectClass::getObject('PageEvent');

DataBaseClass::FromTable('Discipline'); 
DataBaseClass::OrderClear('Discipline','Name'); 
DataBaseClass::Where_current("Status='Active'");    
$disciplines=DataBaseClass::QueryGenerate();
$TrainingEvents=[];
?>
    <?php foreach($disciplines as $d=>$discipline_row){
            $TrainingEvents[]=$discipline_row;
    } ?>
<script>
    scramble='';
</script>    
<?php 
$Event=ObjectClass::getObject('PageEvent');
$Event_CodeScript=$Event['Discipline_CodeScript'];
?>
<hr>
<h1><?= ImageEvent($Event_CodeScript)?> <?= $Event['Discipline_Name']; ?> / <?= ml('TrainingScrambling.Title') ?></h1>
<?php
$exists_GenerateTraining=file_exists("Functions/GenerateTraining_{$Event_CodeScript}.php");
$exists_Generate=file_exists("Functions/Generate_{$Event_CodeScript}.php");
$exists_ScriptGenerate=file_exists("Script/{$Event_CodeScript}_generator.js");

$ScrambleImageFilename='Scramble/Training/'.session_id().'_'.$Event_CodeScript.'.png';

if(($exists_GenerateTraining or $exists_Generate or $exists_ScriptGenerate)
        and file_exists("Scramble/$Event_CodeScript.php")){
    $Scramble='';
    if($exists_GenerateTraining or $exists_Generate){
        if($exists_Generate){
            $Scramble=GenerateScramble($Event_CodeScript,true);
        }
        if($exists_GenerateTraining){
            eval("\$Scramble=GenerateTraining_$Event_CodeScript();");
        }
        include "Scramble/$Event_CodeScript.php";
        $ScrambleImage=ScrambleImage($Scramble,true);
        imagepng($ScrambleImage,$ScrambleImageFilename);
    }elseif($exists_ScriptGenerate){ ?>
        <script src="<?= PageLocal()?>Script/<?= $Event_CodeScript ?>_generator.js" type="text/javascript"></script>
        <script>
            scramble=getscrambles(1);
        </script>
        
    <?php } ?>
        
        
<table width="100%">
<tr>
<td>
    <table class="table_info">
        <tr>
            <td>Extra event</td>
            <td>
                <select onchange="document.location='<?= PageIndex()?>' + this.value ">
                <?php
                foreach($TrainingEvents as $event){?>   
                    <option value="Event/<?= $event['Discipline_Code'] ?>/Training" <?= $event['Discipline_CodeScript']==$Event_CodeScript?'selected':''?> >
                        <?= $event['Discipline_Name'] ?>
                    </option>
                <?php } ?>    
                </select>                
            </td>
        </tr> 
        <?php $Instructions=$Event['Discipline_ScrambleComment'];
        if($Instructions){ ?>
        <tr>
            <td>Inctruction</td>
            <td><?= str_replace("\n","<br>",$Instructions); ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td>Scramble</td>
            <td style="font-size:18px; font-family: monospace; border:1px solid black">
                <span ID="Scramble"><?= str_replace("&","<br>",$Scramble); ?></span>
            </td>    
        </tr>    
        <tr>
            <td>Next scramble</td>
            <td>Press the [SPACE] to generate new scramble</td>
        </tr>    
        <tr><td><hr></td><td><hr></td></tr>
        <?= EventBlockLinks($Event,'training',true); ?>
    </table> 
</td><td>            
    <div style="width:400px;height:300px">
        <img ID="ScrambleImage" style="max-width: 100%; max-height: 100%;" src="<?= !$exists_ScriptGenerate?(PageIndex().$ScrambleImageFilename.'?t='.time()):''?>">
    </div>   
</td>
</tr>
</table>        
        
<script>
    if(scramble){
        $('#Scramble').html(scramble);
        
        $.get( '<?= PageAction('AJAX.Scramble.Image') ?>?CodeScript=<?= $Event_CodeScript ?>&Scramble=' + encodeURI(scramble), function( data ) {
	      $('#ScrambleImage').attr('src',data+ '?t=<?= time() ?>');
         });
    }
</script>
<script>
function moveRect(e){
    switch(e.keyCode){
        case 32: 
            e.preventDefault();
            location.reload();
            break;
    }
}
addEventListener("keydown", moveRect);
</script>            
<?php }else{ ?>
    <i class="fas fa-exclamation-triangle"></i> The event uses an external scramble generator.
<?php }?>

