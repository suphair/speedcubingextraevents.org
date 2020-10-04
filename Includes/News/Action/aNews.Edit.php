<?php

RequestClass::CheckAccessExit(__FILE__, 'aNews');
CheckPostIsset('ID', 'anews');
CheckPostNotEmpty('ID', 'anews');
CheckPostIsNumeric('ID');

$ID = $_POST['ID'];
$Anews = $_POST['anews'];
foreach ($Anews as $c => $anews) {
    if (DataBaseClass::Escape($anews)) {
        $Anews[$c] = $anews;
    } else {
        unset($Anews[$c]);
    }
}

DataBaseClass::Query("Update `News` set Text ='" . DataBaseClass::Escape(json_encode($Anews, JSON_UNESCAPED_UNICODE)) . "' where ID=$ID");

header('Location: ' . PageIndex() . "/News");
exit();
