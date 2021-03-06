<?php
CheckPostIsset('ID','Secret');
CheckPostNotEmpty('ID','Secret');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];
checkingScoreTakerAccess($ID,$Secret);

DataBaseClass::Query("Delete from `Attempt` where Command='$ID' ");

DataBaseClass::FromTable("Command","ID=".$ID);
DataBaseClass::Join_current("Event");
DataBaseClass::Join_current("Competition");
DataBaseClass::Join("Event","DisciplineFormat");
DataBaseClass::Join_current("Discipline");
$data=DataBaseClass::QueryGenerate(false);
    
if(DataBaseClass::QueryGenerate(false)['Command_Onsite']){    
    $teamNames=[];
    DataBaseClass::FromTable('Command',"ID='".$ID."'");
    DataBaseClass::Join_current('CommandCompetitor');
    DataBaseClass::Join_current('Competitor');
    foreach(DataBaseClass::QueryGenerate() as $row){
        $teamNames[]=$row['Competitor_Name'];
    }
    
    LogsRegistration($data['Event_ID'],'S x',implode(", ", $teamNames));
    
    AddLog("CompetitionRegistration","Delete/ScoreTaker",$ID.' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);
    
    
    DataBaseClass::Query("Delete from `CommandCompetitor` where Command='$ID' ");
    DataBaseClass::Query("Delete from `Command` where ID='$ID' ");
}else{
    DataBaseClass::Query("Update `Command` set Decline=1,Place=0,Warnings=null where ID='$ID' ");
}
    
Update_Place($data['Event_ID']);

SetMessage(""); 
header('Location: '.$_SERVER['HTTP_REFERER']);

if(isset($_POST['EventLocalID'])){
    SetMessageName('EventLocalID',$_POST['EventLocalID']);
}
exit();  