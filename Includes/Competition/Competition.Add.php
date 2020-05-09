<?php

$delegate = getDelegate();
$delegates = Delegate::getDelegates();

$data = arrayToObject([
    'competitionAddExt' => CheckAccess('Competition.Add.Ext'),
    'delegates' => $delegates,
    'delegate' => [
        'name' => $delegate['Delegate_Name'],
        'id' => $delegate['Delegate_ID']
    ],
    'error' => GetMessage("CompetitionCreate")
        ]);

IncludeClass::Template('Competition.Add', $data);
