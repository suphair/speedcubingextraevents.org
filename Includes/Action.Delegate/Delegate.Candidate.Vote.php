<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegate.Candidate.Vote');

CheckPostIsset('RequestCandidate','Reason','Status');
CheckPostNotEmpty('RequestCandidate','Status');
CheckPostIsNumeric('RequestCandidate','Status');
$RequestCandidate=$_POST['RequestCandidate'];
$Reason = DataBaseClass::Escape($_POST['Reason']);

DataBaseClass::FromTable("RequestCandidate","ID=$RequestCandidate");
DataBaseClass::Where_current("Status=0");
DataBaseClass::Join_current("Competitor");
$data=DataBaseClass::QueryGenerate(false);

if($data['RequestCandidate_ID']){
    $Competitor=$data['RequestCandidate_Competitor'];
    $Delegate=getDelegate()['Delegate_ID'];
    $Vote=$_POST['Status'];
    DataBaseClass::Query("Delete from RequestCandidateVote where Competitor=$Competitor and Delegate=$Delegate");
    DataBaseClass::Query("Insert into RequestCandidateVote (Competitor,Delegate,Status,Reason) values ($Competitor,$Delegate,$Vote,'$Reason')");    
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
