<?php

function createDumpMain() {
    $_details = [];
    $date = date('dmY_His');
    $filename = "Backup_sql/SEE_main_$date.sql";

    $_details['sql'] = createBackup('schema', $filename);
    $_details['structure'] = generateStructure('schema');

    return json_encode($_details);
}
