<?php 
AddLog('CreateDumpExport', 'Cron','Start');
generateExportData();
DeleteFiles('Export_sql');
crateBackup('schema_Export','Export_sql/SEE_export_'.date('dmY_His').'.sql');
DeleteFiles('Export_tsv');
crateBackupTSV('schema_Export','Export_tsv/SEE_export_'.date('dmY_His'));
AddLog('CreateDumpExport', 'Cron','End'); 
exit();?>