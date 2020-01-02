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
        $updateName=(strlen($row['Command_Name'])==0 or substr($row['Command_Name'],0,1)==' ');
        unset($commands[$row['Command_ID']]);
        if(!isset($dateUpdate[$row['Command_ID']])){
            $dateUpdate[$row['Command_ID']]['Competitors']=0;
            $dateUpdate[$row['Command_ID']]['Country']=$row['Competitor_Country'];
            $dateUpdate[$row['Command_ID']]['Name']='';
            $dateUpdate[$row['Command_ID']]['Sum333']=0;
            
        }
        
        $dateUpdate[$row['Command_ID']]['Competitors']++;
        if($dateUpdate[$row['Command_ID']]['Country']!=$row['Competitor_Country']){
             $dateUpdate[$row['Command_ID']]['Country']="";    
        }
        
        if(strpos($row['Discipline_CodeScript'],'cup_')!==false){
            DataBaseClassWca::Query("select best from `Results` where `personId`='".$row['Competitor_WCAID']."' and eventId='333' order by best limit 1");
            $dateUpdate[$row['Command_ID']]['Sum333']+=DataBaseClassWca::getRow()['best'];
        }
        
        if($updateName){
             if($row['Competitor_WCAID']){
                 $dateUpdate[$row['Command_ID']]['Name'].=' '.substr($row['Competitor_WCAID'],4,4);
             }else{
                 $dateUpdate[$row['Command_ID']]['Name'].=' '.strtoupper(substr($row['Competitor_Name'],0,4));
             }
        }
    }
    
    foreach($commands as $commandID=>$tmp){
        DataBaseClass::Query("Delete from Command where ID=".$commandID);
    }
    
  
   foreach($dateUpdate as $ID=>$data){
       
       
       
       DataBaseClass::Query("Update Command set "
               . " vCompetitors='".$data['Competitors']."',"
               . " vCountry='".$data['Country']."',"
               . " Name=".(isset($data['Name'])?("'".$data['Name']."'"):"Name").", "
               . " Sum333=".$data['Sum333']." "
               #. " vCompetitorIDs='". DataBaseClass::Escape($data['ID'])."'"
               . " where ID=$ID");
   } 
   
}