<?php

RequestClass::CheckAccessExit(__FILE__, 'Delegate.Candidate.GenerateCode');

CheckPostIsset('WCAID');
CheckPostNotEmpty('WCAID');
$wcaid = strtoupper(DataBaseClass::Escape($_POST['WCAID']));

$person = getPersonWcaApi($wcaid, 'Delegate.GenerateCode');

if (!$person) {
    $message = "Person with WCA ID <b>$wcaid</b> not found";
    SetMessageName('Candidate.GenerateCode', $message);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$code = getCodeCandidate($wcaid);
$delegate = getCompetitor()->wca_id;
$message = "The link only works for {$person->name} ({$person->wca_id}):<br> https:" . PageIndex() . "Delegate/Candidate?code=" . $code;
SetMessageName('Candidate.GenerateCode', $message);

DataBaseClass::Query("
    INSERT INTO CandidateCode(
        Delegate,
        Candidate
    )VALUES(
       '$delegate',
       '$wcaid' 
    )   
");


header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
