<?php
CheckPostIsset('ID','Type');
CheckPostNotEmpty('ID','Type');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Type=$_POST['Type'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
    RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$Competition);  
    if(availableCupChange($ID) and availableCupDistribution($ID)){
        CupDistribution($ID,$Type);
    }
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();