<?php

$data = Log::getLogsRegistrations();

IncludeClass::Template('Logs.Registrations', $data);
    