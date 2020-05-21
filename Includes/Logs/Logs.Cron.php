<?php

$logs = Log::getLogsCron();
$objects = Log::getLogsCronObjects();

$data = (object)[
    'logs'=>$logs,
    'objects'=>$objects,
    
];
IncludeClass::Template('Logs.Cron', $data);
