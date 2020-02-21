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

    if($_FILES['scramble']['error']==0 and $_FILES['scramble']['type'] == 'application/pdf'){    

        $rand= random_string(20);  
        $file="Image/Scramble/".$rand.".pdf";

        copy($_FILES['scramble']['tmp_name'],$file);
        DataBaseClass::Query("Insert into ScramblePdf (Event,Secret,Delegate,Timestamp,Action) values ('$ID','$rand','". getDelegate()['Delegate_ID']."','$Scramble_Timestamp','Publication')");  
        DataBaseClass::Query("Update Event set ScramblePublic='$rand' where ID='$ID'"); 
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  