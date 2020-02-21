<?php
RequestClass::CheckAccessExit(__FILE__, 'aNews');
CheckPostIsset('anews');
$Anews= $_POST['anews'];
foreach($Anews as $c=>$anews){
    if(DataBaseClass::Escape($anews)){
        $Anews[$c]=$anews;
    }else{
        unset($Anews[$c]);
    }
}

DataBaseClass::Query("Insert `News` (Date,Text,Delegate)"
        . " values (now(),'". DataBaseClass::Escape(json_encode($Anews,JSON_UNESCAPED_UNICODE))."',". getCompetitor()->id.")");

header('Location: '. PageIndex()."News");
exit();  