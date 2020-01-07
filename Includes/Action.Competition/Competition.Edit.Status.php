<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings',$ID);

$Registration=0+isset($_POST['Registration']);
$Status=0+isset($_POST['Status']);
$Onsite=0+isset($_POST['Onsite']);

if(CheckAccess('Competition.Settings.Ext',$ID)){
    $Unofficial=1-isset($_POST['Unofficial']);
    $DelegateWCAOn=0+isset($_POST['DelegateWCAOn']);
    $Cubingchina=0+isset($_POST['Cubingchina']);
    DataBaseClass::Query("Update `Competition` set Registration='$Registration',Status='$Status',Onsite='$Onsite',Unofficial='$Unofficial',DelegateWCAOn='$DelegateWCAOn',Cubingchina='$Cubingchina' where `ID`='$ID'");
}else{
    DataBaseClass::Query("Update `Competition` set Registration='$Registration',Status='$Status',Onsite='$Onsite' where `ID`='$ID'");
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
