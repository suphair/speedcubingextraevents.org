<?php

RequestClass::CheckAccessExit(__FILE__, 'BackDoor');

$ID = DataBaseClass::Escape(filter_input(INPUT_POST, 'ID'));
if (!$ID) {
    SetMessageName('BackDoor', 'Empty ID');
} else {
    if (is_numeric($ID)) {
        $competitors = DataBaseClass::getRowsAssoc("SELECT * FROM Competitor WHERE WID = $ID");
    } else {
        $competitors = DataBaseClass::getRowsAssoc("SELECT * FROM Competitor WHERE LOWER(WCAID) = LOWER('$ID')");
    }

    if (count($competitors) > 1) {
        SetMessageName('BackDoor', 'More then one competitor found: <br>' . print_r($competitors[0], true));
    }
    if (count($competitors) == 0) {
        SetMessageName('BackDoor', 'No one competitor found');
    }
    if (count($competitors) == 1) {
        AddLog('BackDoor', 'Use', getCompetitor()->name ?? FALSE);
        $competitor = (object) [
                    'local_id' => $competitors[0]['ID'],
                    'name' => $competitors[0]['Name'],
                    'wca_id' => $competitors[0]['WCAID'],
                    'id' => $competitors[0]['WID'],
                    'country_iso2' => $competitors[0]['Country'],
                    'delegate_status' => null
        ];
        $_SESSION['Competitor'] = $competitor;
        $_SESSION['competitorWid'] = $competitor->id;
        AddLog('BackDoor', 'Login', getCompetitor()->name);
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
