<?php

$data = Log::getLogsAuthorisations();

IncludeClass::Template('Logs.Authorisations', $data);
    