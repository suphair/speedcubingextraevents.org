<?php
CheckPostIsset('Event');
CheckPostIsNumeric('Event');
CheckPostNotEmpty('Event');
$ID=$_POST['Event'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);
if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
    
    RequestClass::CheckAccessExit(__FILE__, "Competition.Settings",$Competition);
    $Scramble_Timestamp=date("Y-m-d H:i:s");    
    $rand= random_string(20);  

    DataBaseClass::Query("Insert into ScramblePdf (Event,Secret,Delegate,Timestamp,Action) values ('$ID','$rand','". CashDelegate()['Delegate_ID']."','$Scramble_Timestamp','Cancel Publication')");  
    DataBaseClass::Query("Update Event set ScramblePublic=null where ID='$ID'"); 

}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  