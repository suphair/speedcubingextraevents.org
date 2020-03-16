<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegate.Candidates.Settings');

CheckPostIsset('Action');
CheckPostNotEmpty('Action');
$Action=$_POST['Action'];

if($Action==ml('*.Save',false)){
    CheckPostIsset('ID','Name','Type','Language');
    CheckPostNotEmpty('ID','Name','Type','Language');
    CheckPostIsNumeric('ID');
    
    $ID=$_POST['ID'];
    $Name=$_POST['Name'];
    $Type=$_POST['Type'];
    $Language= strtoupper($_POST['Language']);
    DataBaseClass::Query("Update RequestCandidateTemplate set Language='$Language', Name='$Name',Type='$Type'  where ID=$ID");    
}

if($Action==ml('*.Delete',false)){
    CheckPostIsset('ID');
    CheckPostNotEmpty('ID');
    CheckPostIsNumeric('ID');
    
    $ID=$_POST['ID'];
    DataBaseClass::Query("Delete from RequestCandidateTemplate where ID=$ID");     
}


if($Action==ml('*.Add',false)){
    CheckPostIsset('Name','Type','Language');
    CheckPostNotEmpty('Name','Type','Language');
    
    $Language= strtoupper($_POST['Language']);
    $Name=$_POST['Name'];
    $Type=$_POST['Type'];
    DataBaseClass::Query("Insert into RequestCandidateTemplate (Name, Type,Language) values ('$Name','$Type','$Language')");    
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
