<?php

$data = Log::getLogsCron();

IncludeClass::Template('Logs.Cron', $data);
