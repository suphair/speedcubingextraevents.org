<?php includePage('Navigator'); ?>
<?php includePage('Events.Trainig_Line'); ?>
<?php $Event=ObjectClass::getObject('PageEvent');
$Event_CodeScript=$Event['Discipline_CodeScript'];
?>
<hr>
<h2><?= ImageEvent($Event_CodeScript,30)?> <?= $Event['Discipline_Name']; ?></h1>
<?php
if((file_exists("Functions/GenerateTraining_$Event_CodeScript.php")
        or  
    file_exists("Functions/Generate_$Event_CodeScript.php")) 
        and file_exists("Scramble/$Event_CodeScript.php")){
    
    include "Scramble/$Event_CodeScript.php";
    if(file_exists("Functions/Generate_$Event_CodeScript.php")){
        $Scramble=GenerateScramble($Event_CodeScript,true);
    }else{
        eval("\$Scramble=GenerateTraining_$Event_CodeScript();");
    }
    $ScrambleImage=ScrambleImage($Scramble);
    $ScrambleImageFilename='Scramble/Training/'.session_id().'_'.$Event_CodeScript.'.png';
    imagepng($ScrambleImage,$ScrambleImageFilename); ?>
<table>
    <tr>
        <td>    
            <?php $Instructions=$Event['Discipline_ScrambleComment'];
            if($Instructions){ ?>
                <div style="font-size:20px;" class="border_warning">
                    <?= str_replace("\n","<br>",$Instructions); ?>
                </div>
            <?php  }?>
            <div style="width:600px; font-size:20px;" class="block_comment"><?= str_replace("&","<br>",$Scramble); ?></div>
        </td>
        <td>
            <div style="width:400px;height:400px">
                <img style="max-width: 100%; max-height: 100%;" src="<?= PageIndex().$ScrambleImageFilename?>">
            </div>
        </td>
    </tr>
</table>
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
