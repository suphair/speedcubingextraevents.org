<?php
CheckPostIsset('ID','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Competitors','Groups','Format');
CheckPostNotEmpty('ID','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Groups','Format');
CheckPostIsNumeric('ID','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Groups','Format');
$ID=$_POST['ID'];
$CutoffMinute=$_POST['CutoffMinute'];
$CutoffSecond=$_POST['CutoffSecond'];
$LimitMinute=$_POST['LimitMinute'];
$LimitSecond=$_POST['LimitSecond'];
if(!is_numeric($_POST['Competitors']) or !$_POST['Competitors']){
    $Competitors=500;
}else{
    $Competitors=$_POST['Competitors'];
}
$Groups=$_POST['Groups'];
$Format=$_POST['Format'];
    
DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];

    RequestClass::CheckAccessExit(__FILE__, "Competition.Settings",$Competition);

    $Cumulative=isset($_POST['Cumulative'])?1:0;

    DataBaseClass::Query("Update `Event` set "
            . " `CutoffMinute`='$CutoffMinute',`CutoffSecond`='$CutoffSecond', "
            . " `LimitMinute`='$LimitMinute',`LimitSecond`='$LimitSecond', "
            . " `Competitors`='$Competitors', Groups='$Groups', "
            . " `Cumulative`='$Cumulative', "
            . " `DisciplineFormat`='$Format' "
            . " where ID='$ID' ");
}
SetMessage("");
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitionEvent.Action');
exit();  
