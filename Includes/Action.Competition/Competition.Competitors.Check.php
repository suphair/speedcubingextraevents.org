<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings',$ID);

DataBaseClass::FromTable("Competition","ID=$ID");
$Competition= DataBaseClass::QueryGenerate(false);

$registrations=getCompetitionRegistrationsWcaApi($Competition['Competition_WCA'],'competitionCompetitorsCheck');
if($registrations){    
    
    DataBaseClass::Query("Update CommandCompetitor set CheckStatus=0 where Command in("
            . " Select Com.ID from Command Com join Event E on E.ID=Com.Event where E.Competition='$ID')");
    foreach($registrations as $registration){
        
        DataBaseClass::FromTable("Competitor","WID=".$registration->user_id);
        $competitor=DataBaseClass::QueryGenerate(false);
        if(isset($competitor['Competitor_ID'])){   
            DataBaseClass::Query("Update CommandCompetitor set CheckStatus=1 "
                    . " where Competitor =".$competitor['Competitor_ID']);
        }
    } 
    DataBaseClass::Query("Update Competition set CheckDateTime=current_timestamp where ID='$ID'");  
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  