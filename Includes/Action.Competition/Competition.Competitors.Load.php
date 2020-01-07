<?php

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings.Ext',$ID);

DataBaseClass::FromTable("Competition","ID=$ID");
$Competition= DataBaseClass::QueryGenerate(false);
if(isset($Competition['Competition_ID'])){
    if($Competition['Competition_Cubingchina']){
        CompetitionCompetitorsLoadCubingchina($Competition['Competition_ID'],$Competition['Competition_Name'],'Delegate');
    }else{
        CompetitionCompetitorsLoad($Competition['Competition_ID'],$Competition['Competition_WCA'],$Competition['Competition_Name'],'Delegate');
    }
}
        
header('Location: '.$_SERVER['HTTP_REFERER']);
exit(); 