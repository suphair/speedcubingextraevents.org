<?php AddLog('SendPHPErrors', 'Cron','Start');

$errors= file_get_contents('PHPError.txt');
if(trim($errors)){
    SendMail(getini('Support','email'),'SEE:PHPError ', str_replace("\n","<br>",$errors));
    AddLog('SendPHPErrors', 'Cron', substr_count($errors,'[PHPError]'));
}

exit();