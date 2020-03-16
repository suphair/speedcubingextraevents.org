<?php

CheckPostIsset('ID','Secret','Attempt');
CheckPostNotEmpty('ID','Secret','Attempt');
CheckPostIsNumeric('ID','Attempt');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];
$Attempt=$_POST['Attempt'];
checkingScoreTakerCupAccess($ID,$Secret);

DataBaseClass::Query("Select * from `CupValue` CV join CupCell CC on CC.ID=CV.CupCell "
        . " where CC.Status='run' "
        . " and CV.ID='$Attempt' "
        . " and CC.ID='$ID' "
        . " and Status='run'");

if(DataBaseClass::getAffectedRows()){
    DataBaseClass::Query("Delete from `CupValue` where ID= $Attempt");    
}

DataBaseClass::Query("Select * from `CupValue` where CupCell= $ID");
if(!DataBaseClass::getAffectedRows()){
    DataBaseClass::Query("Update `CupCell` CC "
        . " join `CupCell` CC_prev on CC_prev.ID=CC.CupCell1 or CC_prev.ID=CC.CupCell2 "
        . " left join `CupValue` CV on CV.CupCell=CC.ID "
        . "set CC_prev.Status='done' where CC.ID=$ID and CC_prev.Status='fix' and CV.ID is null");  
}

SetMessage(); 
header('Location: '.$_SERVER['HTTP_REFERER'].'#'.$ID);
exit();  