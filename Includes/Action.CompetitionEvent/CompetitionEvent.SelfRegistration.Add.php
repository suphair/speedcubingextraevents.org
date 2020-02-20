<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$EventID=$_POST['ID'];

DataBaseClass::FromTable('Event',"ID='$EventID'");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('Event','Competition');
$event=DataBaseClass::QueryGenerate(false);
if(count($event)==0){   
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}
$WCA=$event['Competition_WCA'];

$Competitor=GetCompetitorData();

if(!$Competitor){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

DataBaseClass::FromTable("Competitor","WID='".$Competitor->id."'");
$row=DataBaseClass::QueryGenerate(false);
$competitorID=$row['Competitor_ID'];
$competitorName=$row['Competitor_Name'];

DataBaseClass::Join_current("CommandCompetitor");
DataBaseClass::Join_current("Command");
DataBaseClass::Where("Command","Event='$EventID'");
DataBaseClass::QueryGenerate();
if(DataBaseClass::rowsCount()){ 
   header('Location: '.$_SERVER['HTTP_REFERER']);
   exit();  
}

$find=false;
DataBaseClass::Query("Select * from Registration where Competition=".$event['Competition_ID']." and Competitor='".$Competitor->local_id."'");
if(!DataBaseClass::rowsCount()){ 
    CompetitionCompetitorsLoad($event['Competition_ID'],$event['Competition_WCA'],$event['Competition_Name'],'Competitor');
    DataBaseClass::Query("Select * from Registration where Competition=".$event['Competition_ID']." and Competitor='".$Competitor->local_id."'");
    if(DataBaseClass::rowsCount()){ 
        $find=true;  
    }
}else{
    $find=true;
}

if(!$find){
    SetMessageName("RegistrationError", ml('CompetitionEvent.SelfRegistration.Add.NotFind',$Competitor->name,false));
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();    
}


    DataBaseClass::Query("Select count(*) Count from `Command` where Event='".$event['Event_ID']."' and ".$event['Discipline_Competitors']."=vCompetitors");
    if(DataBaseClass::getRow()['Count']<$event['Event_Competitors']){

        if(isset($_POST['Secret']) and $_POST['Secret']){  
            $Secret= strtoupper(trim(DataBaseClass::Escape($_POST['Secret'])));
            DataBaseClass::FromTable('Command',"Event='$EventID'");
            DataBaseClass::Where_current("Secret='$Secret'");

            $commandID=DataBaseClass::QueryGenerate(false)['Command_ID'];


            if(!$commandID){
                SetMessageName("CompetitionRegistrationKey", ml('CompetitionEvent.SelfRegistration.Add.KeyError',false));
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit(); 
            }
            $Command=CommandAdd($commandID,$event['Event_ID'],$competitorID);   

            DataBaseClass::FromTable("Command","ID=".$Command);
            DataBaseClass::Join_current("Event");
            DataBaseClass::Join_current("Competition");
            DataBaseClass::Join("Event","DisciplineFormat");
            DataBaseClass::Join_current("Discipline");
            $data=DataBaseClass::QueryGenerate(false);

            $teamNames=[];
            DataBaseClass::FromTable('Command',"ID='".$Command."'");
            DataBaseClass::Join_current('CommandCompetitor');
            DataBaseClass::Join_current('Competitor');
            foreach(DataBaseClass::QueryGenerate() as $row){
                $teamNames[]=$row['Competitor_Name'];
            }
            
            LogsRegistration($data['Event_ID'],'C +',$competitorName .': '. implode(", ", $teamNames));
            AddLog("CompetitionRegistration","Join team",$Command.' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);

        }else{
            $Command=CommandAdd(0,$event['Event_ID'],$competitorID);   

            DataBaseClass::FromTable("Event","ID=".$event['Event_ID']);
            DataBaseClass::Join_current("Competition");
            DataBaseClass::Join("Event","DisciplineFormat");
            DataBaseClass::Join_current("Discipline");
            $data=DataBaseClass::QueryGenerate(false);

            LogsRegistration($data['Event_ID'],'C *',$competitorName);
            AddLog("CompetitionRegistration",$data["Discipline_Competitors"]>1?"Create team":"Create",$Competitor->name.' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);
    }
    }else{
       header('Location: '.$_SERVER['HTTP_REFERER']);
       exit();  
    }     

header('Location: '.$_SERVER['HTTP_REFERER']);
exit(); 
