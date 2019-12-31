<?php
RequestClass::CheckAccessExit(__FILE__, 'Competitor.Reload');

CheckPostIsset('Competitor');
CheckPostNotEmpty('Competitor');
CheckPostIsnumeric('Competitor');

$ID=$_POST['Competitor'];
$Competitor=DataBaseClass::SelectTableRow('Competitor',"ID=$ID");

$userID=false;
if($Competitor['Competitor_WID']){
    $userID=$Competitor['Competitor_WID'];
}elseif($Competitor['Competitor_WCAID']){
    $userID=$Competitor['Competitor_WCAID'];
}

if(Competitors_Reload($ID,$userID)){
    Competitors_RemoveDuplicates();
    SetMessageName('Competitor.Reload',"Update by $userID complete");
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  