<?php AddLog('LoadRegistrations','Cron','Start');

DataBaseClass::FromTable("Competition","StartDate>now()");
DataBaseClass::Where_current("WCA not like 't.%' ");
$Competitions=DataBaseClass::QueryGenerate();
foreach($Competitions as $Competition){
    if($Competition['Competition_Cubingchina']){
        CompetitionCompetitorsLoadCubingchina($Competition['Competition_ID'],$Competition['Competition_Name'],'Cron');
    }else{
        CompetitionCompetitorsLoad($Competition['Competition_ID'],$Competition['Competition_WCA'],$Competition['Competition_Name'],'Cron');
    }
}

AddLog('LoadRegistrations', 'Cron','End');
exit();  