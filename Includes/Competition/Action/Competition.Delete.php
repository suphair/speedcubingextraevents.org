<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings.Ext');

$ID=$_POST['ID'];

DataBaseClass::Query("Delete from `CompetitionReport` where `Competition`='$ID'");
DataBaseClass::Query("Delete from `CompetitionDelegate` where `Competition`='$ID'");
DataBaseClass::Query("Delete from `Competition` where `ID`='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
