<?php
CheckPostIsset('ID','Rounds');
CheckPostNotEmpty('ID','Rounds');
CheckPostIsNumeric('ID','Rounds');
$ID=$_POST['ID'];
$Rounds=$_POST['Rounds'];


DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
    RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$Competition);
    if(availableCupChange($ID)){
        $CommandsCup=json_decode($row['Event_CommandsCup'],true);
        $CommandsCup['Round']=$Rounds;
        $CommandsCup['Count']=pow(2,$Rounds);
        DataBaseClass::Query("Update Event set CommandsCup='".json_encode($CommandsCup)."' where ID=$ID");
    }
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();
