<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegate.Settings.Ext');

$Delegate=getDelegate();

DataBaseClass::Query("Delete from DelegateChange");
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
