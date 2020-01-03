<?php

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings.Ext',$ID);

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

DataBaseClass::FromTable("Competition","ID=$ID");
$Competition= DataBaseClass::QueryGenerate(false);
if(isset($Competition['Competition_ID'])){
    CompetitionCompetitorsLoad($Competition['Competition_ID'],$Competition['Competition_WCA'],$Competition['Competition_Name'],'Delegate');
}
        
header('Location: '.$_SERVER['HTTP_REFERER']);
exit(); 