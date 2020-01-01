<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegate.Settings.Ext');

$Delegate=CashDelegate();

DataBaseClass::Query("Delete from DelegateChange");
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
