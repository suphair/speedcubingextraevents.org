<?php

$delegate = getDelegate();

$delegates= DataBaseClass::getRowsObject("
    SELECT
        ID id,
        Name name,
        Status status
    FROM Delegate
    ORDER BY Name
");


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
