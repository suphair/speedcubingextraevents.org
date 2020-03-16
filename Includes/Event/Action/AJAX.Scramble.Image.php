<?php
if(!isset($_GET['CodeScript']) or !$_GET['CodeScript']){
    exit();
}

if(!isset($_GET['Scramble']) or !$_GET['Scramble']){
    exit();
}

$CodeScript=$_GET['CodeScript'];
$Scramble= urldecode($_GET['Scramble']);
if(!file_exists("Scramble/$CodeScript.php")){
    exit();   
}

$ScrambleImageFilename='Scramble/Training/'.session_id().'_'.$CodeScript.'.png';
include "Scramble/$CodeScript.php";
$ScrambleImage=ScrambleImage($Scramble);
imagepng($ScrambleImage,$ScrambleImageFilename);
echo PageIndex().$ScrambleImageFilename;
exit();
?>        


