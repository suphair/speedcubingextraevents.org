<?php
RequestClass::CheckAccessExit(__FILE__, 'Event.Settings');

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
DataBaseClass::Query("Update `Discipline` "
        . "set Status='Archive'"
        . " where `ID`='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
