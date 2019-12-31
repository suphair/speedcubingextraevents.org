<?php

AddLog('LoadRegistrations', 'Cron','begin');

DataBaseClass::FromTable("Competition","StartDate>now()");
DataBaseClass::Where_current("WCA not like 't.%' ");
$Competitions=DataBaseClass::QueryGenerate();
foreach($Competitions as $Competition){
    CompetitionCompetitorsLoad($Competition['Competition_ID'],$Competition['Competition_WCA'],$Competition['Competition_Name'],'Cron');
}
AddLog('LoadRegistrations', 'Cron','end');
exit();  