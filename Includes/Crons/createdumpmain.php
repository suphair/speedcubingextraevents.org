<?php 
AddLog('CreateDumpMain', 'Cron','Start');
crateBackup('schema','Backup_sql/SEE_main_'.date('dmY_His').'.sql');
AddLog('CreateDumpMain', 'Cron','End'); 
exit();?>