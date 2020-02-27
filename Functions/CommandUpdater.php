<?php

function CommandUpdateCompetitor($Competitor){
    DataBaseClass::FromTable("CommandCompetitor","Competitor=$Competitor");
    DataBaseClass::Join_current("Command");
    
    foreach(DataBaseClass::QueryGenerate() as $command){
        CommandUpdate('',$command['Command_ID']);
    }
    
    
    
}

function CommandUpdate($Event='',$Command=''){
    DataBaseClass::FromTable("Command");
    if($Command){
        DataBaseClass::Where("Command","ID=$Command");
    }
    if($Event){
        DataBaseClass::Where("Command","Event=$Event");
    }
    
    $commands=array();
    foreach(DataBaseClass::QueryGenerate() as $com){
        $commands[$com['Command_ID']]=1;
    }
    
    DataBaseClass::Join_current("CommandCompetitor");
    DataBaseClass::Join_current("Competitor");
    DataBaseClass::OrderClear("Competitor","Name");
    
    DataBaseClass::Join("Command","Event");
    DataBaseClass::Join_current("DisciplineFormat");
    DataBaseClass::Join_current("Discipline");
    
    
    $dateUpdate=array();
    
    $rows=DataBaseClass::QueryGenerate();

    
    foreach($rows as $row){
        unset($commands[$row['Command_ID']]);
        if(!isset($dateUpdate[$row['Command_ID']])){
            $dateUpdate[$row['Command_ID']]['Competitors']=0;
            $dateUpdate[$row['Command_ID']]['Country']=$row['Competitor_Country'];
            $dateUpdate[$row['Command_ID']]['Sum333']=0;
            
        }
        
        $dateUpdate[$row['Command_ID']]['Competitors']++;
        if($dateUpdate[$row['Command_ID']]['Country']!=$row['Competitor_Country']){
             $dateUpdate[$row['Command_ID']]['Country']="";    
        }
        
        if(strpos($row['Discipline_CodeScript'],'_cup')!==false){
            DataBaseClassWca::Query("select best from `RanksAverage` where `personId`='".$row['Competitor_WCAID']."' and eventId='333'");
            $best=DataBaseClassWca::getRow()['best'];
            if($best<=0 or $dateUpdate[$row['Command_ID']]['Sum333']==999999){
                $dateUpdate[$row['Command_ID']]['Sum333']=999999;
            }else{
                $dateUpdate[$row['Command_ID']]['Sum333']+=$best;
            }
        }
    }
    
    foreach($commands as $commandID=>$tmp){
        DataBaseClass::Query("Delete from Command where ID=".$commandID);
    }
    
 
   foreach($dateUpdate as $ID=>$data){
       if($data['Competitors']!=$row['Discipline_Competitors']){
           $data['Sum333']=999999;
       }
       
       DataBaseClass::Query("Update Command set "
               . " vCompetitors='".$data['Competitors']."',"
               . " vCountry='".$data['Country']."',"               
               . " Sum333=".$data['Sum333']." "
               . " where ID=$ID");
       
       DataBaseClass::Query("Update Command set Name='#$ID' where ID=$ID and Name is null");
   } 
}