<?php

function CashDelegate(){
    if(!$Delegate=ObjectClass::getObject('Delegate')){
        if(isset($_SESSION['Competitor'])){
            DataBaseClass::FromTable("Delegate","WCA_ID='".$_SESSION['Competitor']->wca_id."'");
            DataBaseClass::Where_current("Status!='Archive'");
            $Delegate=DataBaseClass::QueryGenerate(false);
            
            if(DataBaseClass::rowsCount()){
                ObjectClass::setObjects('Delegate', $Delegate);
                return $Delegate;
            }
        }
    }
    return $Delegate;
}

Function CashDelegateCompetition($Competition){
    if(!$Delegate=CashDelegate()){
        return false;
    }
   
    if(!$DelegateCompetition=ObjectClass::getObject("DelegateCompetition")){
        DataBaseClass::FromTable("Delegate","WCA_ID='".$Delegate['Delegate_WCA_ID']."'");    
        DataBaseClass::Join_current("CompetitionDelegate");
        DataBaseClass::Join_current("Competition");
        if(is_numeric($Competition)){
            DataBaseClass::Where_current("ID='$Competition'");
        }else{   
            DataBaseClass::Where_current("WCA='$Competition'");
        }
        DataBaseClass::Select("1");
        $DelegateCompetition=DataBaseClass::QueryGenerate(false);
        if(DataBaseClass::rowsCount()){
            ObjectClass::setObjects("DelegateCompetition", $DelegateCompetition);
            return $DelegateCompetition;
        }
    }
    return  $DelegateCompetition;
}

Function CashDelegateCompetitionEvent($CompetitionEvent){
    if(!$Delegate=CashDelegate()){
        return false;
    }
   
    if(!$DelegateCompetitionEvent=ObjectClass::getObject("DelegateCompetitionEvent")){
        DataBaseClass::FromTable("Delegate","WCA_ID='".$Delegate['Delegate_WCA_ID']."'");    
        DataBaseClass::Join_current("CompetitionDelegate");
        DataBaseClass::Join_current("Competition");
        DataBaseClass::Join_current("Event");
        DataBaseClass::Where_current("ID='$CompetitionEvent'");
        DataBaseClass::Select("1");
        $DelegateCompetitionEvent=DataBaseClass::QueryGenerate(false);
        if(DataBaseClass::rowsCount()){
            ObjectClass::setObjects("DelegateCompetition", $DelegateCompetitionEvent);
            return $DelegateCompetitionEvent;
        }
    }
    return  $DelegateCompetitionEvent;
}


function CheckAccess($type,$competitionID=false){
    if(!GetCompetitorData() and !cashDelegate()){
        return false;
    }
        
    $DelegateID=cashDelegate()['Delegate_ID'];
    
    if(!$Level=ObjectClass::getObject('GrandRole')){
        DataBaseClass::Query("Select Level from GrandRole where Name='".cashDelegate()['Delegate_Status']."'");
        $row=DataBaseClass::getRow();
        if(isset($row['Level'])){
            $Level=$row['Level'];
        }else{
            $Level=0;
        }
        ObjectClass::setObjects('GrandRole', $Level);
    }
    

    $result=0;
    
    
    DataBaseClass::Query("Select * from GrandGroupMember GGM join GrandGroup GG on GG.ID=GGM.Group join GrandAccess GA on GA.Group=GG.ID where GGM.Delegate='$DelegateID' and Type='$type'");
    $result+=sizeof(DataBaseClass::getRows());
    
    
    DataBaseClass::Query("Select 1 from GrandAccess where Type='$type' and Level<=$Level and Competition=0 ");
    $result+=sizeof(DataBaseClass::getRows());
    if($competitionID){
        $CompetitionCheck=false;
        DataBaseClass::Query("Select * from CompetitionDelegate where Competition='".$competitionID."' and Delegate='$DelegateID'");
        if(isset(DataBaseClass::getRow()['ID'])){$CompetitionCheck=true;}
        
        DataBaseClass::Query("Select * from Competition where ID='".$competitionID."' and DelegateWCAOn=1 and DelegateWCA like '%".(GetCompetitorData()->wca_id)."%'");
        if(isset(DataBaseClass::getRow()['ID'])){$CompetitionCheck=true;}
       
        if($CompetitionCheck){
            DataBaseClass::Query("Select 1 from GrandAccess where Type='$type' and Level<=$Level and Competition=1");
            $result+=sizeof(DataBaseClass::getRows());

        }
        
        
    }
    
    
    return $result>0;
}