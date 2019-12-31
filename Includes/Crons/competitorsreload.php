<?php
$start=date("d.m.Y H:i:s");

$userIDs=[];
DataBaseClass::Query("
Select UpdateTimestamp,C.ID,C.WID CID from Competitor C  where  C.WID is not null and WCAID='' and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > 7
union
Select UpdateTimestamp,C.ID,C.WCAID CID from Competitor C  where  C.WID is null and WCAID!=''  and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > 7
order by UpdateTimestamp Limit 25");

foreach(DataBaseClass::getRows() as $user){
    $userIDs[$user['ID']]=$user['CID'];
}
 
foreach($userIDs as $ID=>$userID){
    Competitors_Reload($ID,$userID);
}

Competitors_RemoveDuplicates();

$end=date("d.m.Y H:i:s");
SaveValue('Competitors.Reload',$start." - ".$end." &#9642; ".sizeof($userIDs));
AddLog('CompetitorsReload', 'Cron',sizeof($userIDs));

exit();  