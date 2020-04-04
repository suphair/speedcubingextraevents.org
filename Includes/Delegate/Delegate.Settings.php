<?php

$data->statuses = [
    'Senior',
    'Middle',
    'Junior',
    'Trainee',
    'Archive'
    ];
$data->accessSettingExt = CheckAccess('Delegate.Settings.Ext');
$competitions = Competition::getCompetitionsIdbyDelegate($data->id);
    $data->availableForDeletion = empty($competitions);
IncludeClass::Template('Delegate.Settings', $data);
