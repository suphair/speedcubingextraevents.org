<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegate.Settings');
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
DataBaseClass::Query("Delete from `CompetitionReport` where `Competition`='$ID'");
DataBaseClass::Query("Delete from `Delegate` where `ID`='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
