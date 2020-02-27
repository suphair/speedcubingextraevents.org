<?php 
AddLog('CreateDumpExport', 'Cron','Start');
generateExportData();
crateBackup('schema_Export','Export_sql/SEE_export_'.date('dmY_His').'.sql');
crateBackupTSV('schema_Export','Export_tsv/SEE_export_'.date('dmY_His'));
AddLog('CreateDumpExport', 'Cron','End'); 
exit();?>