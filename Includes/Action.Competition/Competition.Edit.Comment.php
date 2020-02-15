<?php
CheckPostIsset('ID','Comment');
CheckPostNotEmpty('ID');
CheckPostIsnumeric('ID');

$ID=$_POST['ID'];

RequestClass::CheckAccessExit(__FILE__, "Competition.Settings",$ID);

$Comments= $_POST['Comment'];
foreach($Comments as $c=>$Comment){
    if(DataBaseClass::Escape($Comment)){
        $Comments[$c]=$Comment;
    }else{
        unset($Comments[$c]);
    }
}

DataBaseClass::Query("Update `Competition`set Comment='". DataBaseClass::Escape(json_encode($Comments,JSON_UNESCAPED_UNICODE))."'  where `ID`='$ID'");

header('Location: '.$_SERVER['HTTP_REFERER'].'#Competition.Edit.Comment');
exit();  