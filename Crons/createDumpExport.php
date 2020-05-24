<?php

$date = date('dmY_His');
generateExportData();
DeleteFiles('Export_sql');
$_details['sql'] = createBackup('schema_Export', "Export_sql/SEE_export_$date.sql");
DeleteFiles('Export_tsv');
$_details['tsv'] = createBackupTSV('schema_Export', "Export_tsv/SEE_export_$date");

