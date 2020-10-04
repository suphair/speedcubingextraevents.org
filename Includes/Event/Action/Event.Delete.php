<?php
RequestClass::CheckAccessExit(__FILE__, 'Event.Settings');

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
DataBaseClass::Query("Delete from `Discipline` where `ID`='$ID'");

SetMessage("Delegate Deleted $ID");


header('Location: '. PageIndex()."/Events");
exit();  
