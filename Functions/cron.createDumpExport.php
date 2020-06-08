<?php

function createDumpExport() {
    $_details = [];
    $date = date('dmY_His');
    generateExportData();
    DeleteFiles('Export_sql');
    $_details['sql'] = createBackup('schema_Export', "Export_sql/SEE_export_$date.sql");
    DeleteFiles('Export_tsv');
    $_details['tsv'] = createBackupTSV('schema_Export', "Export_tsv/SEE_export_$date");

    return json_encode($_details);
}
