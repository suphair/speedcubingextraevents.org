<?php

RequestClass::CheckAccessExit(__FILE__, 'Event.Settings');

CheckPostIsset('regulation','ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');


    
$Regulations= $_POST['regulation'];
foreach($Regulations as $c=>$regulation){
    $Regulations[$c]=DataBaseClass::Escape($regulation);
}
$ID=$_POST['ID'];

foreach($Regulations as $language=>$regulation){
    if(in_array($language, getLanguages())){
        DataBaseClass::Query("REPLACE into `Regulation` (Event,Language,Text)"
        . " values ($ID,'$language','$regulation')");
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();

