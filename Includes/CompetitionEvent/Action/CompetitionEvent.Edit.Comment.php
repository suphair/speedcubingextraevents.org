<?php


CheckPostIsset('ID','Comment');
CheckPostNotEmpty('ID');
CheckPostIsnumeric('ID');

$ID=$_POST['ID'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];

    RequestClass::CheckAccessExit(__FILE__, "Competition.Event.Settings",$Competition);
    $Comments= $_POST['Comment'];
    foreach($Comments as $c=>$Comment){
        if(DataBaseClass::Escape($Comment)){
            $Comments[$c]=$Comment;
        }else{
            unset($Comments[$c]);
        }
    }
    DataBaseClass::Query("Update `Event` set Comment='". DataBaseClass::Escape(json_encode($Comments,JSON_UNESCAPED_UNICODE))."'  where `ID`='$ID'");
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

