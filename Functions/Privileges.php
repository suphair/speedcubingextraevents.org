<?php

Function GetCompetitorData(){
    if(isset($_SESSION['Competitor'])){        
        if(!isset($_SESSION['Competitor']->id)){
            unset($_SESSION['Competitor']);
            return false;        
        }
        
        return $_SESSION['Competitor'];
    }
    return false;
}


function CheckingScoreTakerCompetitor($CommandID,$Secret){
    DataBaseClass::Query("Select Com.ID from `Command` Com join `Event` E on E.ID=Com.Event  where Com.`ID`='$CommandID' and E.Secret='$Secret'");
    if(!DataBaseClass::rowsCount()){  
        SetMessage("score taker access denied");
        HeaderExit(); 
    }   
}

function GetScoreTakerEvent($Secret){
    DataBaseClass::Query("Select ID from `Event` E where E.Secret='$Secret'");
    
    if(!DataBaseClass::rowsCount()){  
        SetMessage("score taker not exists");
        HeaderExit(); 
    }   
    return DataBaseClass::getRow()['ID'];
}

function CheckingScoreTakerEvent($EventID,$Secret){
    DataBaseClass::Query("Select E.ID from  `Event` E where E.`ID`='$EventID' and E.Secret='$Secret'");
    
    if(!DataBaseClass::rowsCount()){  
        SetMessage("score taker access denied");
        HeaderExit(); 
    }   
}