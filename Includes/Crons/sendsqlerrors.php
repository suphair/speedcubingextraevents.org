<?php

AddLog('SendSqlErrors', 'Cron','run');

$errors= file_get_contents('SQLError.txt');
if(trim($errors)){
    SendMail(getini('Support','email'),'SEE:SqlErrors ', Echo_format($errors));
}

exit();