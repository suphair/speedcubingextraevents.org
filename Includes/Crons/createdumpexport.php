<?php 
AddLog('CreateDumpExport', 'Cron','Start');
Script_exportData();
Script_backup();
AddLog('CreateDumpExport', 'Cron','End'); 
exit();?>