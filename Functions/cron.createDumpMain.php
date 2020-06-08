<?php

function createDumpMain() {
    $_details = [];
    $date = date('dmY_His');
    $filename = "Backup_sql/SEE_main_$date.sql";

    $_details['sql'] = createBackup('schema', $filename);

    return json_encode($_details);
}
