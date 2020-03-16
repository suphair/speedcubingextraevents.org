<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegate.Candidate.Decline');
CheckPostIsset('RequestCandidate');
CheckPostNotEmpty('RequestCandidate');
CheckPostIsNumeric('RequestCandidate');
$RequestCandidate=$_POST['RequestCandidate'];

DataBaseClass::FromTable("RequestCandidate","ID=$RequestCandidate");
DataBaseClass::Where_current("Status=0");
DataBaseClass::Join_current("Competitor");
$data=DataBaseClass::QueryGenerate(false);

if($data['RequestCandidate_ID']){
    DataBaseClass::Query("Update RequestCandidate set Status=-1 where ID=$RequestCandidate");    
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
