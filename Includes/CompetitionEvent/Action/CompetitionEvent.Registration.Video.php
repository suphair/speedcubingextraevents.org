<?php

CheckPostIsset('ID','Video');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

DataBaseClass::FromTable('Command');
DataBaseClass::Where_current("ID='$ID'");
DataBaseClass::Join_current('Event');

$row=DataBaseClass::QueryGenerate(false);
if(isset($row['Event_ID'])){
    $EventID=$row['Event_ID'];
    $CompetitionID=$row['Event_Competition'];
}else{
    echo 'Wrong command '.$ID;
    exit();
}
RequestClass::CheckAccessExit(__FILE__, "Competition.Event.Settings",$CompetitionID);

$Video= DataBaseClass::Escape($_POST['Video']);

DataBaseClass::FromTable('Command');
DataBaseClass::Where_current("ID='$ID'");
DataBaseClass::Join_current('Event');
$Command=DataBaseClass::QueryGenerate(false);
DataBaseClass::Query("Update Command set Video='$Video' where ID=$ID");


SetMessage();    
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  

