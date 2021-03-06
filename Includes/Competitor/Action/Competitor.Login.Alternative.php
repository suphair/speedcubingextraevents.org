<?php

CheckPostIsset('Secret', 'Secret');
CheckPostNotEmpty('Secret');

$Secret = DataBaseClass::Escape($_POST['Secret']);

DataBaseClass::Query("Select C.ID Local_ID, D.Name, D.WCA_ID, D.WID,C.Country from  Delegate D "
        . " join Competitor C on C.WID=D.WID "
        . " where Status<>'Archive' and Secret<>'' and Secret='$Secret'");
$Delegate = DataBaseClass::getRow();
if (is_array($Delegate)) {
    $competitor = (object) [
                'local_id' => $Delegate['Local_ID'],
                'name' => $Delegate['Name'],
                'wca_id' => $Delegate['WCA_ID'],
                'id' => $Delegate['WID'],
                'country_iso2' => $Delegate['Country'],
                'delegate_status' => null
    ];
    if (isset($_POST['WCA'])) {
        $competitor->delegate_status = 'delegate';
    }

    $_SESSION['Competitor'] = $competitor;
    $_SESSION['competitorWid'] = $competitor->id;
    AddLog('Alternative', 'Login', $Delegate['Name']);
} else {
    SetMessageName('Alternative', 'Access denied');
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
