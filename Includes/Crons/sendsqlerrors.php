<?php AddLog('SendSQLErrors', 'Cron','Start');

$errors= file_get_contents('SQLError.txt');
if(trim($errors)){
    SendMail(getini('Support','email'),'SEE:SqlErrors ',  str_replace("\n","<br>",$errors));
    AddLog('SendSQLErrors', 'Cron', substr_count($errors,'[SQLError]'));
}
exit();