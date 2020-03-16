<?php

CheckPostIsset('ID','Secret','Winner');
CheckPostNotEmpty('ID','Secret','Winner');
CheckPostIsNumeric('ID','Winner');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];
$Winner=$_POST['Winner'];


checkingScoreTakerCupAccess($ID,$Secret);

DataBaseClass::Query("Update `CupCell` "
        . " set Status='done',"
        . " CommandWin=$Winner"
        . " where ID='$ID' and Status='run' and $Winner in (Command1,Command2)");

if(DataBaseClass::getAffectedRows()){
    DataBaseClass::Query("Update `CupCell` set Command1=$Winner where CupCell1=$ID");
    DataBaseClass::Query("Update `CupCell` set Command2=$Winner where CupCell2=$ID");
    
    DataBaseClass::Query("Update `CupCell` set Status='run' where $ID in (CupCell1,CupCell2) and Command1 and Command2");
    DataBaseClass::Query("Update `CupCell` CC join `CupCell` CC_prev on CC_prev.ID=CC.CupCell1 or CC_prev.ID=CC.CupCell2 "
            . "set CC_prev.Status='fix' where CC.ID=$ID and CC_prev.Status='done'");
}

SetMessage(); 
header('Location: '.$_SERVER['HTTP_REFERER'].'#'.$ID);
exit();  