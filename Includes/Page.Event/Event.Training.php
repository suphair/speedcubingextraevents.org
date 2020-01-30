<script>
    scramble='';
</script>    
<?php includePage('Navigator'); ?>
<?php includePage('Events.Trainig_Line'); ?>
<?php $Event=ObjectClass::getObject('PageEvent');
$Event_CodeScript=$Event['Discipline_CodeScript'];
?>
<hr>
<h2><?= ImageEvent($Event_CodeScript,30)?> <?= $Event['Discipline_Name']; ?></h1>
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
        $ScrambleImage=ScrambleImage($Scramble);
        imagepng($ScrambleImage,$ScrambleImageFilename);
    }elseif($exists_ScriptGenerate){ ?>
        <script src="<?= PageLocal()?>Script/<?= $Event_CodeScript ?>_generator.js" type="text/javascript"></script>
        <script>
            scramble=getscrambles(1);
        </script>
        
    <?php }
    
    ?>
<table>
    <tr>
        <td>    
            <?php $Instructions=$Event['Discipline_ScrambleComment'];
            if($Instructions){ ?>
                <div style="font-size:20px;" class="border_warning">
                    <?= str_replace("\n","<br>",$Instructions); ?>
                </div>
            <?php  }?>
            <div ID="Scramble" style="width:600px; font-size:20px;" class="block_comment">   
            <?= str_replace("&","<br>",$Scramble); ?></div>
            <div>Press the space to generate new scramble</div>
        </td>
        <td>
            <div style="width:400px;height:400px">
                <img ID="ScrambleImage" style="max-width: 100%; max-height: 100%;" src="<?= !$exists_ScriptGenerate?(PageIndex().$ScrambleImageFilename):''?>">
            </div>
        </td>
    </tr>
</table>
<script>
    if(scramble){
        $('#Scramble').html(scramble);
        
        $.get( '<?= PageAction('AJAX.Scramble.Image') ?>?CodeScript=<?= $Event_CodeScript ?>&Scramble=' + encodeURI(scramble), function( data ) {
	      $('#ScrambleImage').attr('src',data);
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

