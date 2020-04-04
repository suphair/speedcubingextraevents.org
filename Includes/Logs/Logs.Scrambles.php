<?php

$data = Log::getLogsScrambles();

IncludeClass::Template('Logs.Scrambles', $data);
