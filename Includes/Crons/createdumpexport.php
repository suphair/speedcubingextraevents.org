<?php 
AddLog('CreateDumpExport', 'Cron','Start');
generateExportData();
crateBackup('schema_Export','Export_sql/SEE_export_'.date('dmY_His').'.sql');
AddLog('CreateDumpExport', 'Cron','End'); 
exit();?>