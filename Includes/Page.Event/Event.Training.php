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
    
        
<?php }else{ ?>
    <snap class="error">The event uses an external scramble generator.</span>
<?php }?>

<?php exit(); ?>

<?php


if(!isset($_GET['Discipline']) or !in_array($_GET['Discipline'],
        array('Redi','2x2x3','Dino','Ivy','Kilominx'))){
    echo 'wrong discipline';
    exit();
}
$discipline=$_GET['Discipline'];

if(!isset($_SESSION[$discipline.'_N'])){
    $_SESSION[$discipline.'_N']=1;
}else{
    $_SESSION[$discipline.'_N']++;
}
$Scrumble_ID=$discipline.'_'.session_id();

include 'Functions/GenerateScramble.php';
include 'Functions/Generate'.$discipline.'.php';
include 'Functions/Rotate.php';

$scramble_function='Generate'.$discipline;
$scramble=$scramble_function();
include 'Scramble/'.$discipline.'.php';
$scramble=str_replace(array("x  R","x R","x  & R"),"x<br>R",$scramble);
$scramble=str_replace(array("x  L","x L","x  & L"),"x<br>   L",$scramble);
$scramble=str_replace("& ","<br>",$scramble);
$scramble=str_replace(" ","&nbsp;",$scramble);
$scramble=str_replace("x","<font color=red>x</font>",$scramble);

?>
<h1><?= $discipline ?>: <?=  $_SESSION[$discipline.'_N'] ?></h1>
<table cellpadding="20px" border=1>
    <tr>
        <td><img width="320px" src="../Image/Scramble/<?= $Scrumble_ID ?>.png?tmp=<?= time()?>"></td>
        <td align=left><?= $scramble ?></td>
    </tr>   
</table>
<br>
<span style="font-size:24px;" >Click the Mouse or Press the Space or Tap the Screen</span>

<script>

function moveRect(e){
    switch(e.keyCode){
        case 32: 
            location.reload();
            break;
    }
}

addEventListener("keydown", moveRect);

addEventListener('click', function(){
   location.reload();
});

addEventListener('touchend', function(){
   location.reload();
});


</script>
