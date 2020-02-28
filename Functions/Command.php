<?php
function CommandAdd($CommandID,$EventID,$CompetitorID,$onsite=0){
    if(!$CommandID){
        
        DataBaseClass::Query("Select C.ID,C.MaxCardID from Competition C join `Event` E on E.Competition=C.ID where E.ID='$EventID'");  
        $row=DataBaseClass::getRow();
        $CardID=$row['MaxCardID']+1;              
        $CompetitionID=$row['ID'];
        DataBaseClass::Query("Update `Competition` set MaxCardID='$CardID' where ID='$CompetitionID'");  
        
        DataBaseClass::Query("Insert "
                . " into `Command` (Event,Secret,CardID,Onsite) "
                . " values ('$EventID','".strtoupper(random_string(8))."',$CardID,$onsite)");
        $CommandID=DataBaseClass::getID();
    }
    
    DataBaseClass::Query("REPLACE INTO `CommandCompetitor` (`Command`,`Competitor`) values ('$CommandID','$CompetitorID')");
    
    CommandUpdate($EventID,$CommandID);
            
    return $CommandID;
}

function SortCommandOrder($a,$b){
    if($a['Result']['vOrder'] and !$b['Result']['vOrder']){
        return false;
    }
    if(!$a['Result']['vOrder'] and $b['Result']['vOrder']){
        return true;
    }
    
    if($a['ExtResult']['vOrder'] and !$b['ExtResult']['vOrder']){
        return false;
    }
    if(!$a['ExtResult']['vOrder'] and $b['ExtResult']['vOrder']){
        return true;
    }
    
    
    if($a['Result']['vOrder']!=$b['Result']['vOrder']){
        return $a['Result']['vOrder']>$b['Result']['vOrder'];
    }
    if($a['ExtResult']['vOrder']!=$b['ExtResult']['vOrder']){
        return $a['ExtResult']['vOrder']>$b['ExtResult']['vOrder'];
    }
    return $a['Name']>$b['Name'];
}

function SortCommandCupOrder($a,$b){
    if($a['Result']['Sum333'] and !$b['Result']['Sum333']){
        return false;
    }
    if(!$a['Result']['Sum333'] and $b['Result']['Sum333']){
        return true;
    }
    
    if($a['Result']['Sum333']!=$b['Result']['Sum333']){
        return $a['Result']['Sum333']>$b['Result']['Sum333'];
    }
    
    return $a['Name']>$b['Name'];
}

function SortCommandCupOrderResult($a,$b){
    if($a['loses']!=$b['loses']){
        return $a['loses']>$b['loses'];
    }
    if($a['wins']!=$b['wins']){
        return $a['wins']<$b['wins'];
    }    
    return $a['Command_Name']>$b['Command_Name'];
}


