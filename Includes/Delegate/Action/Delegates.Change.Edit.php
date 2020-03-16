<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegates.Settings');

$Delegate=getDelegate();
#DataBaseClass::Query("Delete from DelegateChange where Senior=".$Delegate['Delegate_ID']);

CheckPostIsset('Delegate');

foreach($_POST['Delegate'] as $delegate=>$status){
    $delegate= DataBaseClass::Escape($delegate);
    $status= DataBaseClass::Escape($status);
    
    if(is_numeric($delegate)){
        DataBaseClass::Query("INSERT INTO DelegateChange set Delegate='$delegate', Senior=".$Delegate['Delegate_ID']." ,Status='$status'");
        #DataBaseClass::Query("Insert into  DelegateChange (Delegate,Senior,Status) values ('$delegate',".$Delegate['Delegate_ID'].",'$status')");
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
