<script>
    scramble='';
</script>    
<?php includePage('Navigator'); ?>
<?php includePage('Events.Trainig_Line'); ?>
<?php $Event=ObjectClass::getObject('PageEvent');
$Event_CodeScript=$Event['Discipline_CodeScript'];
?>
<hr>
<h1><?= ImageEvent($Event_CodeScript,50)?> <?= $Event['Discipline_Name']; ?> / <?= ml('TrainingScrambling.Title') ?></h1>
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
        
    <?php }
    
    ?>
<table width="100%">
    <tr>
        <td width="400px">
            <div style="width:400px;height:300px">
                <img ID="ScrambleImage" style="max-width: 100%; max-height: 100%;" src="<?= !$exists_ScriptGenerate?(PageIndex().$ScrambleImageFilename.'?t='.time()):''?>">
            </div>
            Press the <b>space</b> to generate new scramble
        </td>
        <td>    
            <span class="form" ID="Scramble" style="font-size:20px;">   
            <?= str_replace("&","<br>",$Scramble); ?></span>
            <?php $Instructions=$Event['Discipline_ScrambleComment'];
            if($Instructions){ ?>
                <div><?= str_replace("\n","<br>",$Instructions); ?></div>
            <?php  }?>
        </td>
    </tr>
</table>
<?= EventBlockLinks($Event,'training'); ?>
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
    <snap class="error">The event uses an external scramble generator.</span>
<?php }?>

