<?php

RequestClass::CheckAccessExit(__FILE__, 'Delegate.Settings');

CheckPostIsset('ID', 'Contact', 'Secret');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID = $_POST['ID'];

$Secret = DataBaseClass::Escape($_POST['Secret']);
$Contact = DataBaseClass::Escape($_POST['Contact']);

if (CheckAccess('Delegate.Settings.Ext')) {
    $Status = $_POST['Status'];
    if (in_array($Status, ['Senior', 'Middle', 'Junior', 'Trainee', 'Archive'])) {
        DataBaseClass::Query("Update `Delegate` set  Contact='$Contact' , Secret='$Secret',Status='$Status'  where ID='$ID'");
    }
} else {
    DataBaseClass::Query("Update `Delegate` set  Contact='$Contact', Secret='$Secret'  where ID='$ID'");
}
SetMessage();
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
