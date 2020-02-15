<?php

CheckPostIsset('ID','Status','Registration','Onsite');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID','Status','Registration','Onsite');
if(CheckAccess('Competition.Settings.Ext',$ID)){
    CheckPostIsset('Unofficial','DelegateWCAOn','Cubingchina');
    CheckPostIsNumeric('Unofficial','DelegateWCAOn','Cubingchina');
}

$ID=$_POST['ID'];

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings',$ID);

$Registration=$_POST['Registration'];
$Status=$_POST['Status'];
$Onsite=$_POST['Onsite'];

if(CheckAccess('Competition.Settings.Ext',$ID)){
    $Unofficial=$_POST['Unofficial'];
    $DelegateWCAOn=$_POST['DelegateWCAOn'];
    $Cubingchina=$_POST['Cubingchina'];
    DataBaseClass::Query("Update `Competition` set Registration='$Registration',Status='$Status',Onsite='$Onsite',Unofficial='$Unofficial',DelegateWCAOn='$DelegateWCAOn',Cubingchina='$Cubingchina' where `ID`='$ID'");
}else{
    DataBaseClass::Query("Update `Competition` set Registration='$Registration',Status='$Status',Onsite='$Onsite' where `ID`='$ID'");
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
