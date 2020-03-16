<?php
CheckPostIsset('ID','Secret');
CheckPostNotEmpty('ID','Secret');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];

checkingScoreTakerCupAccess($ID,$Secret);

DataBaseClass::Query("Update `CupCell` "
        . " set Status='run',"
        . " CommandWin=null"
        . " where ID='$ID' and Status='done'");

DataBaseClass::Query("Update `CupCell` set Command1=null,Status='wait' where CupCell1=$ID");
DataBaseClass::Query("Update `CupCell` set Command2=null,Status='wait' where CupCell2=$ID");

DataBaseClass::Query("Update `CupCell` CC "
            . " join `CupCell` CC_prev on CC_prev.ID=CC.CupCell1 or CC_prev.ID=CC.CupCell2 "
            . " left join `CupValue` CV on CV.CupCell=CC.ID "
            . "set CC_prev.Status='done' where CC.ID=$ID and CC_prev.Status='fix' and CV.ID is null");

SetMessage(); 
header('Location: '.$_SERVER['HTTP_REFERER'].'#'.$ID);
exit();  