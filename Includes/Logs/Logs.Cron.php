<?php

$logs = Log::getLogsCron();
$names = Log::getLogsCronNames();

$data = (object)[
    'logs'=>$logs,
    'names'=>$names,
    
];
IncludeClass::Template('Logs.Cron', $data);
