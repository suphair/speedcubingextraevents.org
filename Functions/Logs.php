<?php
Function AddLog($Object,$Action,$Details){
    if(getCompetitor()){
        $CompetitorID=getCompetitor()->id;
    }else{
        $CompetitorID=0;
    }
    $Object= DataBaseClass::Escape($Object);
    $Action= DataBaseClass::Escape($Action);
    $Details= DataBaseClass::Escape($Details);
    DataBaseClass::Query("Insert into Logs (Competitor,Object,Action,Details,IP) values"
            . " ($CompetitorID,'$Object','$Action','$Details','".$_SERVER['REMOTE_ADDR']."') ");
    
}

Function LogsRegistration($EventID,$Action,$Details){
    $Action= DataBaseClass::Escape($Action);
    $Details= DataBaseClass::Escape($Details);
    $Doing='ScoreTaker';
            
    if($Competitor= getCompetitor()){
        $Doing='Competitor: '.Short_Name($Competitor->name);
    }
    
    if($Delegate= getDelegate()){
        $Doing='Delegate: '.Short_Name($Delegate['Delegate_Name']);
    }
    
    DataBaseClass::Query("Insert into LogsRegistration (Event,Action,Details,Doing) values"
            . " ($EventID,'$Action','$Details','$Doing') ");
}