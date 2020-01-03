<?php
RequestClass::CheckAccessExit(__FILE__, 'MainRegulations.Edit');
CheckPostIsset('language','text');
CheckPostNotEmpty('language','text');

$language= DataBaseClass::Escape($_POST['language']);
$text=DataBaseClass::Escape($_POST['text']);

DataBaseClass::Query("Update BlockText set Value='".$text."' where Name='MainRegulation' and Country='$language'");


header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
