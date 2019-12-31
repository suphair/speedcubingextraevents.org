<?php
RequestClass::CheckAccessExit(__FILE__, 'Delegate.Candidate.Accept');
CheckPostIsset('RequestCandidate');
CheckPostNotEmpty('RequestCandidate');
CheckPostIsNumeric('RequestCandidate');
$RequestCandidate=$_POST['RequestCandidate'];

DataBaseClass::FromTable("RequestCandidate","ID=$RequestCandidate");
DataBaseClass::Where_current("Status=0");
DataBaseClass::Join_current("Competitor");
$data=DataBaseClass::QueryGenerate(false);

if($data['RequestCandidate_ID']){
    DataBaseClass::Query("Update RequestCandidate set Status=1 where ID=$RequestCandidate"); 
    
    DataBaseClass::Query("Insert into Delegate(`Name`,`WCA_ID`,`Status`,`WID`) values"
            . " ('".$data['Competitor_Name']."','".$data['Competitor_WCAID']."','Trainee',".$data['Competitor_WID'].")");    
    
    $WCAID=$data['Competitor_WCAID'];
    $Name=$data['Competitor_Name'];
    SendMail(getini('Seniors','email'), 'SEE: New delegate '.$Name,
            "<pre>".$Name." <br>SEE <a href='https://". PageIndex()."Delegate/$WCAID'>$Name</a><br>WCA <a href='https://www.worldcubeassociation.org/persons/$WCAID'>$WCAID</a><br>");
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
