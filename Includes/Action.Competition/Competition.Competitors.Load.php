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
/*
$start=date('H:i:s');
$ID=$Competition['Competition_ID'];
$registrations_data = file_get_contents(GetIni('WCA_API','competition')."/".$Competition['Competition_WCA']."/registrations", false); 
$registrations=json_decode($registrations_data);
if($registrations){    
    foreach($registrations as $registration){
        DataBaseClass::FromTable('Competitor',"WID='".$registration->user_id."'");
        $Competitor=DataBaseClass::QueryGenerate(false);
        if(isset($Competitor['Competitor_ID'])){
            $Competitor_ID=$Competitor['Competitor_ID'];
        }else{
            $user_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/users/".$registration->user_id, false);   
            $user=json_decode($user_content);
            $Competitor_ID=CompetitorReplace($user->user);
        }
        DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
    }
}



$result_data = file_get_contents(GetIni('WCA_API','competition')."/".$Competition['Competition_WCA']."/competitors", false); 
$results=json_decode($result_data);
if($results){    
    foreach($results as $result){
        DataBaseClass::FromTable('Competitor',"WCAID='".$result->wca_id."'");
        $Competitor=DataBaseClass::QueryGenerate(false);
        if(isset($Competitor['Competitor_ID'])){
            $Competitor_ID=$Competitor['Competitor_ID'];
        }else{
            $Competitor_ID=CompetitorReplace($result);
        }

        DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
    }
}

if(sizeof($results)){
    $str=sizeof($registrations)." / ".sizeof($results);
}else{
    $str=sizeof($registrations);
}

$end=date('H:i:s');

DataBaseClass::Query("Update Competition set LoadDateTime=concat(current_timestamp,' &#9642; '"
            . ",'$str'"
            . ") where ID='$ID'");

AddLog('CompetitorsCompetition', 'DelegateReload',$Competition['Competition_Name'].' ('.$str.') '.$start.' - '.$end.' ');

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  */