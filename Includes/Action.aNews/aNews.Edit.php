<?php
RequestClass::CheckAccessExit(__FILE__, 'aNews');
CheckPostIsset('anews','ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID= $_POST['ID'];

if(isset($_POST['Delete'])){
    DataBaseClass::Query("Delete from `News` where ID=$ID");
}else{
    $Anews= $_POST['anews'];
    foreach($Anews as $c=>$anews){
        if(DataBaseClass::Escape($anews)){
            $Anews[$c]=DataBaseClass::Escape($anews);
        }else{
            unset($Anews[$c]);
        }
    }

    DataBaseClass::Query("Update `News` set Text ='". json_encode($Anews,JSON_UNESCAPED_UNICODE)."' where ID=$ID");
}
header('Location: '. PageIndex()."News");
exit();  