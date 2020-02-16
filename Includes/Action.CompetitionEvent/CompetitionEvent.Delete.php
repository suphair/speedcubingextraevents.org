<?php

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];

    RequestClass::CheckAccessExit(__FILE__, "Competition.Settings",$Competition);

    $commands=count(DataBaseClass::SelectTableRows('Command',"Event=$ID"));
    if(!$commands){
        DataBaseClass::Query("Delete from  `Scramble` where `Event`=$ID");
        DataBaseClass::Query("Delete from  `Event` where `ID`=$ID");
    }
    EventRoundView($Competition);
}
SetMessage();
    
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitionEvent.Action');
exit();  