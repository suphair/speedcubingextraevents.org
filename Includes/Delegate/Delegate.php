<?php

$delegate = new Delegate();
$wacid = getPathElement('delegate', 1);
$delegate->getByWcaid($wacid);
if (!$delegate->id) {
    IncludeClass::Template('404', (object) ['error' => "Delegate $wacid not found"]);
} else {

    $data = clone $delegate;
    $data->accessSettings = CheckAccess('Delegate.Settings');

    $action = getPathElement('delegate', 2);
    switch ($action) {
        case 'settings':
            if(CheckAccess("Delegate.Settings")){
                IncludeClass::Page('Delegate.Settings', $data);
            }else{
                IncludeClass::Template('401', (object) ['error' => "Access is denied for delegate settings"]);  
            }
            
            break;
        case false:
            $CompetitionsId = $delegate->getCompetitionsIdbyDelegate();

            $data->competitions = Competition::getCompetitionsByCompetitionsID($CompetitionsId);

            IncludeClass::Template('Delegate', $data);
            IncludeClass::Template('CompetitionsList', $data);
            break;
        default:
            IncludeClass::Template('404', (object) ['error' => "Action $action for delegate not found"]);
    }
}