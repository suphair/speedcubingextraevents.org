<?php

CheckPostIsset('Secret','Type');
CheckPostNotEmpty('Secret','Type');
$Secret=$_POST['Secret'];
$Type=$_POST['Type'];

CheckPostIsset('Selected');
foreach($_POST['Selected'] as $select){
    if(!is_numeric($select)){
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();  
    }
}
    
$EventID=GetScoreTakerEvent($Secret);    


if($Type=='Competitors'){
    foreach($_POST['Selected'] as $competitor){
        DataBaseClass::FromTable("CommandCompetitor","Competitor=$competitor");
        DataBaseClass::Join_current("Command");
        DataBaseClass::Join_current("Event");
        DataBaseClass::Where_current("ID=$EventID");
        DataBaseClass::Join("CommandCompetitor","Competitor");
        $data=DataBaseClass::QueryGenerate(true);
        
        if(DataBaseClass::rowsCount()){
            SetMessageName("ResultsSaveWarning", $data[0]["Competitor_Name"]." already exists");
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();  
        }
    } 
    
    $Command=0;
    foreach($_POST['Selected'] as $competitor){
        $Command=CommandAdd($Command,$EventID,$competitor,true);
    }        
    $ID=$Command;
    
    DataBaseClass::FromTable("Command","ID=".$ID);
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
    
    LogsRegistration($data['Event_ID'],'S +',implode(", ", $teamNames));
    AddLog("CompetitionRegistration","Create/ScoreTaker",$ID.' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);

    
}elseif($Type=='Command'){
    $ID=$_POST['Selected'][0];
}


$Warnings=(isset($_POST['AttempsWarning']))?$_POST['AttempsWarning']:'';
checkingScoreTakerAccess($ID,$Secret);
DataBaseClass::Query("Select FR.Format FormatResult,Com.Event,F.Attemption, F.Result, F.ExtResult "
        . " from `Format` F "
        . " join `DisciplineFormat` DF on DF.Format=F.ID "
        . " join `Discipline` D on DF.Discipline=D.ID "
        . " join `FormatResult` FR on FR.ID=D.FormatResult "
        . " join `Event` E on E.DisciplineFormat=DF.ID "
        . " join `Command` Com on Com.Event=E.ID "
        . "where Com.ID='$ID'");
$format=DataBaseClass::getRow();

$values=array();
$amounts=array();
for($i=1;$i<=$format['Attemption'];$i++){
    CheckPostIsset("Value$i");
    $values[$i]=$_POST["Value$i"];
    if(isset($_POST["Amount$i"]) and is_numeric($_POST["Amount$i"])){
        $amounts[$i]=$_POST["Amount$i"];
    }else{
        $amounts[$i]=0;
    }
}


$warn=SetValueAttempts($ID,$format['Attemption'],$format['Result'],$format['ExtResult'],$values,$amounts);
if(!$warn)$Warnings='';
DataBaseClass::Query("Update Command set Warnings='$Warnings' where ID=$ID");



    DataBaseClass::Query("Select GROUP_CONCAT(C.Name order by C.Name SEPARATOR ', ') vName, Warnings "
            . " from  `Command` Com  "
            . " join CommandCompetitor CC on CC.Command=Com.ID join Competitor C on C.ID=CC.Competitor "
            . " where Com.ID='$ID' "
            . " group by Com.ID ");
    
$command=DataBaseClass::getRow();
foreach($values as $i=>$value){
    if(!$value){
        $values[$i]='-';
    }
    
    if(strpos($format['FormatResult'],"A")!==false){
        $values[$i]=$amounts[$i]." | ".$value;
    }else{
        $values[$i]=$value;    
    }
}
if($command['Warnings']){
    $result=$command['vName'].': ['.implode("], [",$values). ']. Results were saved with warnings. ';
    SetMessageName("ResultsSaveWarning", $result);
}else{
    $result=$command['vName'].': ['.implode("], [",$values). ']. Results were saved.';
    SetMessageName("ResultsSave", $result);
}

Update_Place($format['Event']);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
