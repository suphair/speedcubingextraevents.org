<?php 

$errors= file_get_contents('SQLError.txt');
if(trim($errors)){
    $_details['count'] = substr_count($errors, '[SQLError]');
    $_details['mail'] = SendMail(getini('Support','email'),'SEE:SQLErrors ',  str_replace("\n","<br>",$errors));
}
