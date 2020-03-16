<?php
CheckPostIsset('ID','Command');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Command=$_POST['Command'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
    RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$Competition);
    if(availableCupChange($ID)){
        DataBaseClass::Query("Update Command set inCup=0");
        foreach($Command as $commandID=>$tmp){
            DataBaseClass::Query("Update Command set inCup=1 where ID=".$commandID);    
        }
    }
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();