<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegates.Settings');

$Delegate=CashDelegate();

DataBaseClass::Query("Delete from DelegateChange where Senior=".$Delegate['Delegate_ID']);
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
