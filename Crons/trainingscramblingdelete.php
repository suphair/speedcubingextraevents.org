<?php AddLog('TrainingScramblingDelete', 'Cron','Start');

$deleted=DeleteFiles('Scramble/Training');
if($deleted){
    AddLog('TrainingScramblingDelete', 'Cron',"Deleted $deleted");
}

exit();