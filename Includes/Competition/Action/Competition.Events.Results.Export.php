<?php
$requests=getRequest();
if(!isset($requests[2]) or !is_numeric($requests[2])){
    echo 'Wrong competition ID';
    exit();
}else{
   $ID=$requests[2];
}

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings',$ID);

DataBaseClass::Query("Select C.WCA, C.Name Competition from  Competition C where C.ID='$ID'"); 

if(DataBaseClass::rowsCount()==0){
   if(!sizeof($competitors)){
    echo 'Competition not found';
    exit();
}   
}
$competition=DataBaseClass::getrow();
$results=array();


DataBaseClass::Query("Select D.Code Code, D.Name Discipline, C.Name Competition, F.Result, F.Attemption, E.ID Event from `Event` E "
        . " join Competition C on C.ID=E.Competition"
        . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
        . " join Discipline D on D.ID=DF.Discipline"
        . " join Format F on F.ID=DF.Format where C.ID='$ID'"); 

$events=DataBaseClass::getRows();
foreach($events as $event){
    
    DataBaseClass::Query(" Select GROUP_CONCAT(C.Name order by C.Name) vName, Com.ID from Command Com "
            . " join CommandCompetitor CC on Com.ID=CC.Command "
            . " join Competitor C on C.ID=CC.Competitor "
            . "  where Com.Event='".$event['Event']."' group by Com.ID order by Place");
    $commands=DataBaseClass::getRows();

    foreach($commands as $command){
        
        DataBaseClass::Query(" Select A.Attempt, A.vOut from Attempt A "
                . " where A.Command='".$command['ID']."' order by A.Attempt");
        $attempts=DataBaseClass::getRows();
        foreach($attempts as $attempt){
            if(is_numeric($attempt['Attempt'])){
                $results[$competition['WCA']][$event['Code']][$command['vName']][]=$attempt['vOut'];
            }
        }
    }
}

echo json_encode($results);
exit();