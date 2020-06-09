<?php

function createDumpMain() {
    $_details = [];
    $date = date('dmY_His');
    $filename = "Backup_sql/SEE_main_$date.sql";

    $_details['sql'] = createBackup('schema', $filename);
    $_details['structure'] = generateStructure('schema');
    $_details['schema_Export'] = generateStructure('schema_Export');
    $_details['schema_WCA'] = generateStructure('schema_WCA');

    return json_encode($_details);
}
