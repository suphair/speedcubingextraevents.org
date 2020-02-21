<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$EventID=$_POST['ID'];

$Competitor=getCompetitor();

if(!$Competitor){
    SetMessageName("RegistrationDeleteError", "Not completed the WCA authorization");
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

DataBaseClass::FromTable('Command',"Event='$EventID'");
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Competitor');
DataBaseClass::Where('Competitor',"WID='".$Competitor->id."'");
$Command=DataBaseClass::QueryGenerate(false);
if(!isset($Command['Command_ID'])){   
    SetMessageName("RegistrationDeleteError", "No registration");
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

DataBaseClass::FromTable('Command',"ID='".$Command['Command_ID']."'");
DataBaseClass::Join_current('Attempt');
$Attempt=DataBaseClass::QueryGenerate();
if(count($Attempt)>0){
    SetMessageName("RegistrationDeleteError", "Attempts have already been made");
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();    
    
}   

$teamNames=[];
DataBaseClass::FromTable('Command',"ID='".$Command['Command_ID']."'");
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Competitor');
foreach(DataBaseClass::QueryGenerate() as $row){
    $teamNames[]=$row['Competitor_Name'];
}

DataBaseClass::FromTable("Event","ID=".$EventID);
DataBaseClass::Join_current("Competition");
DataBaseClass::Join("Event","DisciplineFormat");
DataBaseClass::Join_current("Discipline");
$data=DataBaseClass::QueryGenerate(false);
if(sizeof($teamNames)>1){
    LogsRegistration($data['Event_ID'],'C -',$Competitor->name .': '. implode(", ", $teamNames));
}else{
    LogsRegistration($data['Event_ID'],'C x',$Competitor->name);    
}
AddLog("CompetitionRegistration","Delete",$Command['Command_ID'].' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);

DataBaseClass::Query("Delete from `CommandCompetitor` where ID='".$Command['CommandCompetitor_ID']."'");
CommandUpdate($EventID,$Command['Command_ID']);
CommandDeleter();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit(); 
