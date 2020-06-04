<?php

function getObjCompetitor($wcaid = false) {
    if ($wcaid) {
        $competitor = DataBaseClass::getRowObject("
            SELECT 
                Country country,
                Name name,
                WCAID wcaid,
                ID local_id
            FROM Competitor
            WHERE WCAID = '$wcaid'
        ");
        $competitor->country = getObjectCountry($competitor->country);
    } else {
        $competitor = getCompetitor();
    }

    if ($competitor) {
        $competitor->link = PageIndex() . "Competitor/{$competitor->local_id}";
    }
    return $competitor;
}

function getObjDelegate($wcaid = false) {
    if ($wcaid) {
        $delegate = DataBaseClass::SelectTableRow('Delegate', "WCA_ID = '$wcaid'");
    } else {
        $delegate = getDelegate();
    }
    if ($delegate) {
        return arrayToObject([
            'name' => Short_Name($delegate['Delegate_Name']),
            'status' => $delegate['Delegate_Status'],
            'wcaid' => $delegate['Delegate_WCA_ID'],
            'wid' => $delegate['Delegate_WID'],
            'id' => $delegate['Delegate_ID'],
            'link' => LinkDelegate($delegate['Delegate_WCA_ID']),
            'contact' => $delegate['Delegate_Contact'],
            'competitor' => getObjCompetitor($wcaid)
        ]);
    } else {
        return false;
    }
}

function getCompetitor() {
    if (isset($_SESSION['Competitor'])) {
        if (!isset($_SESSION['Competitor']->id)) {
            unset($_SESSION['Competitor']);
            $competitor = false;
        }
        $competitor = $_SESSION['Competitor'];
    } else {
        $competitor = false;
    }
    return $competitor;
}

function checkingScoreTakerAccess($commandID, $secret) {
    DataBaseClass::Query("Select Com.ID "
            . " from `Command` Com "
            . " join `Event` E on E.ID=Com.Event "
            . " where Com.`ID`='" . DataBaseClass::Escape($commandID) . "' "
            . " and E.Secret='" . DataBaseClass::Escape($secret) . "'");
    if (!DataBaseClass::rowsCount()) {
        SetMessage("score taker access denied");
        HeaderExit();
    }
}

function checkingScoreTakerCupAccess($cellID, $secret) {
    DataBaseClass::Query("Select CC.ID "
            . " from `CupCell` CC "
            . " join `Event` E on E.ID=CC.Event "
            . " where CC.`ID`='" . DataBaseClass::Escape($cellID) . "' "
            . " and E.Secret='" . DataBaseClass::Escape($secret) . "'");
    if (!DataBaseClass::rowsCount()) {
        SetMessage("score taker access denied");
        HeaderExit();
    }
}

function GetScoreTakerEvent($Secret) {
    DataBaseClass::Query("Select ID from `Event` E where E.Secret='$Secret'");

    if (!DataBaseClass::rowsCount()) {
        SetMessage("score taker not exists");
        HeaderExit();
    }
    return DataBaseClass::getRow()['ID'];
}

function CheckingScoreTakerEvent($EventID, $Secret) {
    DataBaseClass::Query("Select E.ID from  `Event` E where E.`ID`='$EventID' and E.Secret='$Secret'");

    if (!DataBaseClass::rowsCount()) {
        SetMessage("score taker access denied");
        HeaderExit();
    }
}

function getDelegate() {
    if (!$delegate = ObjectClass::getObject('Delegate')) {
        if ($competitor = getCompetitor()) {
            DataBaseClass::FromTable("Delegate", "WCA_ID='" . $competitor->wca_id . "'");
            DataBaseClass::Where_current("Status!='Archive'");
            if ($delegate = DataBaseClass::QueryGenerate(false)) {
                ObjectClass::setObjects('Delegate', $delegate);
            }
        }
    }
    return $delegate;
}

function CheckAccess($type, $competitionID = false) {
    if (!getCompetitor() and ! getDelegate()) {
        return false;
    }

    $delegate=getDelegate();
    if(!isset($delegate['Delegate_ID']) or !isset($delegate['Delegate_Status'])){
        return false;
    }
    $DelegateID = $delegate['Delegate_ID'];

    if (!$Level = ObjectClass::getObject('GrandRole')) {
        DataBaseClass::Query("Select Level from GrandRole where Name='{$delegate['Delegate_Status']}'");
        $row = DataBaseClass::getRow();
        if (isset($row['Level'])) {
            $Level = $row['Level'];
        } else {
            $Level = 0;
        }
        ObjectClass::setObjects('GrandRole', $Level);
    }


    $result = 0;


    DataBaseClass::Query("Select * from GrandGroupMember GGM join GrandGroup GG on GG.ID=GGM.Group join GrandAccess GA on GA.Group=GG.ID where GGM.Delegate='$DelegateID' and Type='$type'");
    $result += sizeof(DataBaseClass::getRows());


    DataBaseClass::Query("Select 1 from GrandAccess where Type='$type' and Level<=$Level and Competition=0 ");
    $result += sizeof(DataBaseClass::getRows());
    if ($competitionID) {
        $CompetitionCheck = false;
        DataBaseClass::Query("Select * from CompetitionDelegate where Competition='" . $competitionID . "' and Delegate='$DelegateID'");
        if (isset(DataBaseClass::getRow()['ID'])) {
            $CompetitionCheck = true;
        }

        DataBaseClass::Query("Select * from Competition where ID='" . $competitionID . "' and DelegateWCAOn=1 and DelegateWCA like '%" . (getCompetitor()->wca_id) . "%'");
        if (isset(DataBaseClass::getRow()['ID'])) {
            $CompetitionCheck = true;
        }

        if ($CompetitionCheck) {
            DataBaseClass::Query("Select 1 from GrandAccess where Type='$type' and Level<=$Level and Competition=1");
            $result += sizeof(DataBaseClass::getRows());
        }
    }
    return $result > 0;
}
