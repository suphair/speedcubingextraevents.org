<?php

CheckPostIsset('ID','CommandName');
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

$CommandName= DataBaseClass::Escape($_POST['CommandName']);

DataBaseClass::FromTable('Command');
DataBaseClass::Where_current("ID='$ID'");
DataBaseClass::Join_current('Event');
$Command=DataBaseClass::QueryGenerate(false);
DataBaseClass::Query("Update Command set Name='$CommandName' where ID=$ID");


SetMessage();    
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  

