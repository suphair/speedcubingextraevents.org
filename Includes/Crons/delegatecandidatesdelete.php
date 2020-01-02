<?php

AddLog('DelegateCandidatesDelete', 'Cron','Begin');
DataBaseClass::Query(" Select * from RequestCandidate where Status=-1 and (TO_DAYS(now()) - TO_DAYS(Datetime)) >= 263");
foreach(DataBaseClass::getRows() as $row){
    DataBaseClass::Query(" Delete from RequestCandidateVote where Competitor=".$row['Competitor'],true);
    DataBaseClass::Query(" Delete from RequestCandidateField where RequestCandidate=".$row['ID'],true);
    DataBaseClass::Query(" Delete from RequestCandidate where Competitor=".$row['Competitor'],true);
    
    AddLog('DelegateCandidatesDelete', 'Cron',$row['Competitor']." ".$row['Datetime']);
}

exit();