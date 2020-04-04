<?php

$data = Log::getLogsMail();

IncludeClass::Template('Logs.Mail', $data);
