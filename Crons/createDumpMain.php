<?php

$date = date('dmY_His');
$filename = "Backup_sql/SEE_main_$date.sql";

$_details['sql'] = createBackup('schema', $filename);
