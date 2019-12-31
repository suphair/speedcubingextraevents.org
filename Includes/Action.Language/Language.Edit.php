<?php
$_SESSION['ML_ACTION']=true;
RequestClass::CheckAccessExit(__FILE__, 'MultiLanguage');

CheckPostIsset('MultiLanguages');
CheckPostNotEmpty('MultiLanguages');


foreach($_POST['MultiLanguages'] as $Name=>$Languages){
    foreach($Languages as $Language=>$Value){
        if($Value){
            DataBaseClass::Query("REPLACE into MultiLanguage (Name,Value,Language) "
                    . " values ('".DataBaseClass::Escape($Name)."','".DataBaseClass::Escape($Value)."','".DataBaseClass::Escape($Language)."')");
        }else{
            DataBaseClass::Query("Delete from MultiLanguage where Name='".DataBaseClass::Escape($Name)."' and Language='".DataBaseClass::Escape($Language)."'");
        }
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();
