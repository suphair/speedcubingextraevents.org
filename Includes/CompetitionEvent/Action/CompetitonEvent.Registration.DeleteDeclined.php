<?php

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];

    RequestClass::CheckAccessExit(__FILE__, "Competition.Event.Settings",$Competition);

    DataBaseClass::Query("select Com.ID, Com.Decline, count(A.ID) Attempt  "
            . " from Command Com"
            . " left outer join Attempt A on A.Command=Com.ID"
            . " where Com.Event=".$ID.""
            . " group by Com.ID"
            );

    $deleter=true;
    $deleter_IDs=array();
    foreach(DataBaseClass::getRows() as $row){
        if(!$row['Attempt'] and !$row['Decline'] ){
            $deleter=false;  
        }else{
            if($row['Decline']){ 
                $deleter_IDs[]=$row['ID'];
            }
        }
    }

    if($deleter){
        foreach($deleter_IDs as $deleter_ID){

            $teamNames=[];
            DataBaseClass::FromTable('Command',"ID='".$deleter_ID."'");
            DataBaseClass::Join_current('CommandCompetitor');
            DataBaseClass::Join_current('Competitor');
            foreach(DataBaseClass::QueryGenerate() as $row){
                $teamNames[]=$row['Competitor_Name'];
            }

            LogsRegistration($ID,'D x',implode(", ", $teamNames));

            DataBaseClass::Query("Delete from CommandCompetitor where Command=".$deleter_ID);
            DataBaseClass::Query("Delete from Command where ID=".$deleter_ID);
        }
    }
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();