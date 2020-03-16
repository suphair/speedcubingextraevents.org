<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

DataBaseClass::FromTable('Command');
DataBaseClass::Where_current("ID='$ID'");
DataBaseClass::Join_current('Event');
$row=DataBaseClass::QueryGenerate(false);
if(isset($row['Event_ID'])){
    $EventID=$row['Event_ID'];
    $CompetitionID=$row['Event_Competition'];
}else{
    echo 'Wrong command '.$ID;
    exit();
}

RequestClass::CheckAccessExit(__FILE__, "Competition.Event.Settings",$CompetitionID);

DataBaseClass::FromTable('Command');
DataBaseClass::Where_current("ID='$ID'");
DataBaseClass::Join_current('Event');
DataBaseClass::Join('Command','CommandCompetitor');
DataBaseClass::Join_current('Competitor');
$Competitor=DataBaseClass::QueryGenerate(false);
DataBaseClass::Join('Command','Attempt');
$attempt=DataBaseClass::QueryGenerate();
if(sizeof($attempt)){
    SetMessageName("CompetitorEventAddError","Attempts have already been made");
    header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
    exit();     
}

DataBaseClass::FromTable("Command","ID=".$ID);
DataBaseClass::Join_current("Event");
DataBaseClass::Join_current("Competition");
DataBaseClass::Join("Event","DisciplineFormat");
DataBaseClass::Join_current("Discipline");
$data=DataBaseClass::QueryGenerate(false);

$teamNames=[];
DataBaseClass::FromTable('Command',"ID='".$ID."'");
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Competitor');
foreach(DataBaseClass::QueryGenerate() as $row){
    $teamNames[]=$row['Competitor_Name'];
}

LogsRegistration($data['Event_ID'],'D x',implode(", ", $teamNames));
AddLog("CompetitionRegistration","Delete/Delegate",$ID.' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);



DataBaseClass::Query("Delete from `CommandCompetitor` where Command='$ID' ");
DataBaseClass::Query("Delete from `Command` where ID='$ID' ");


SetMessage(); 
    
SetMessageName("CompetitorEventAddMessage",$ID." Deleted");
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  