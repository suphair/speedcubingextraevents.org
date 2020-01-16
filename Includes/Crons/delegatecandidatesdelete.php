<?php AddLog('DelegateCandidatesDelete', 'Cron','Start');

DataBaseClass::Query(" Select RC.Datetime, RC.Competitor, RC.ID,C.Name,C.WCAID from RequestCandidate RC"
        . " join Competitor C on C.ID=RC.Competitor "
        . "where RC.Status=-1 and (TO_DAYS(now()) - TO_DAYS(RC.Datetime)) >= 365");
foreach(DataBaseClass::getRows() as $row){
    DataBaseClass::Query(" Delete from RequestCandidateVote where Competitor=".$row['Competitor']);
    DataBaseClass::Query(" Delete from RequestCandidateField where RequestCandidate=".$row['ID']);
    DataBaseClass::Query(" Delete from RequestCandidate where Competitor=".$row['Competitor']);
    
    AddLog('DelegateCandidatesDelete', 'Cron',"Deleted ".$row['Name']." ".$row['WCAID']." ".$row['Datetime']);
}

exit();