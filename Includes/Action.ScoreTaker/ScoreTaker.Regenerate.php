<?php

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
    RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$Competition);
    DataBaseClass::Query("Update `Event` set `Secret`='". random_string(16)."' where ID='$ID' ");
    SetMessageName("EventGenerateScoreTakerMessage", "Link updated");
}
SetMessage();
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
