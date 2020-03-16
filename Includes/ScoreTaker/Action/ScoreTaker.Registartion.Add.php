<?php

CheckPostIsset('Competition','WCAID');
CheckPostNotEmpty('Competition','WCAID');
CheckPostIsNumeric('Competition');

$Competition=$_POST['Competition'];
RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$Competition);

$WCAID=$_POST['WCAID'];

if(!preg_match('/[0-9]{4}[a-zA-z]{4}[0-9]{2}/', $WCAID, $matches)){
        SetMessageName("ScoreTaker.Registartion.Add", "$WCAID - wrong WCA ID format");   
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
}


DataBaseClass::FromTable('Competitor',"WCAID='$WCAID'");
$Competitor=DataBaseClass::QueryGenerate(false);

if(isset($Competitor['Competitor_ID'])){
    $Competitor_ID=$Competitor['Competitor_ID'];    
}else{
    $user=getUserWcaApi($WCAID, 'scoreTakerRegistration');
    if($user){
        $Competitor_ID=CompetitorReplace($user);
    }else{
        SetMessageName("ScoreTaker.Registartion.Add", "$WCAID not found");   
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }
}
DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$Competition)");
SetMessageName("ScoreTaker.Registartion.Add", "Register $WCAID");

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();


