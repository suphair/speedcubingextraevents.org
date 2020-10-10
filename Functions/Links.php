<?php

function LinkDiscipline($code) {
    return PageIndex() . "/Event/$code";
}

function setLinkEvent($CompetitionWCA, $EventCode, $eventRound) {
    if ($eventRound > 1) {
        return PageIndex() . "/Competition/$CompetitionWCA/$EventCode/$eventRound";
    } else {
        return PageIndex() . "/Competition/$CompetitionWCA/$EventCode";
    }
}

function LinkEvent($ID) {
    DataBaseClass::FromTable('Event', "ID=$ID");
    DataBaseClass::Join('Event', 'Competition');
    DataBaseClass::Join('Event', 'DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    $event = DataBaseClass::QueryGenerate(false);
    return PageIndex() . "/Competition/" . $event['Competition_WCA'] . "/" . $event['Discipline_Code'] . "/" . $event['Event_Round'];
}

function LinkCompetitor($ID, $WCAID = "") {
    return PageIndex() . "/Competitor/" . ($WCAID ? $WCAID : $ID);
}

function LinkCompetition($WCA) {
    return PageIndex() . "/Competition/$WCA";
}

function LinkLogin() {
    return PageIndex() . "/Login";
}

function LinkDelegate($WCAID) {
    return PageIndex() . "/Delegate/$WCAID";
}

function LinkDelegateAdd() {
    return PageIndex() . "/Delegate/Add";
}

function LinkSettingsBack() {
    return "http://" . $_SERVER['HTTP_HOST'] . str_replace("/Settings", "", $_SERVER['REQUEST_URI']);
}

function GetUrlWCA() {
    wcaoauth::set(PageIndex() .'/', DataBaseClass::getConection());

    return Suphair \ Wca \ Oauth::url();
}
