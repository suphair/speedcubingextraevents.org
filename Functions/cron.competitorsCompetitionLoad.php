<?php

function competitorsCompetitionLoad() {
    $_details = [];

    DataBaseClass::FromTable("Competition", "StartDate>now()");
    DataBaseClass::Where_current("WCA not like 't.%' ");
    $Competitions = DataBaseClass::QueryGenerate();
    foreach ($Competitions as $Competition) {
        $_details[] = $Competition['Competition_WCA'];
        if ($Competition['Competition_Cubingchina']) {
            CompetitionCompetitorsLoadCubingchina($Competition['Competition_ID'], $Competition['Competition_Name'], 'Cron');
        } else {
            CompetitionCompetitorsLoad($Competition['Competition_ID'], $Competition['Competition_WCA'], $Competition['Competition_Name'], 'Cron');
        }
    }
    return json_encode($_details);
}
