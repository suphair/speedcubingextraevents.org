<?php

    function Group_Name($n){
        $Group_Name=array(-1=>"","A","B","C","D","E","F");
        return $Group_Name[$n];
    }
    
    function Groups_Name($n){
        $groups=array();
        for($i=0;$i<$n;$i++){
            $groups[]=Group_Name($i);
        }
        return implode(", ", $groups).".";
    }

    
function CommandDeleter(){
    DataBaseClass::Query("Delete from Command where ID not in (Select Command from CommandCompetitor)");
}


function Competitors_Reload($ID,$userID){
    if(!$userID or !$ID)return false;
    
    $user=getUserWcaApi($userID,'competitorReload');
    if($user){    
        DataBaseClass::Query("Update Competitor set "
       . " Name='". Short_Name(DataBaseClass::Escape($user->name))."'"
       . " ,Country='".$user->country_iso2."'"
       . " ,WID='".$user->id."'"
       .($user->wca_id?" , WCAID='".$user->wca_id."'":"")
       . " ,UpdateTimestamp=now() "
        . " where ID=$ID");
        
        DataBaseClass::FromTable("Competitor","ID=$ID");
        DataBaseClass::Join_current("CommandCompetitor");
        DataBaseClass::Join_current("Command");
        foreach(DataBaseClass::QueryGenerate() as $command){
            CommandUpdate('',$command['Command_ID']);
        }       
        return true;
    }else{
        DataBaseClass::Query("Update Competitor set UpdateTimestamp=now() where ID=$ID");
        return false;
    }
}


function Competitors_RemoveDuplicates(){
    DataBaseClass::Query("
        select count(*),WID from Competitor
        where WID is not null
        group by WID
        having count(*)>1
    ");

    foreach(DataBaseClass::getRows() as $Double){
        $WID=$Double['WID'];
        DataBaseClass::Query("
            select ID from Competitor
            where WID = '$WID'
            order by ID desc
        ");
        $Competitors=DataBaseClass::getRows();

        $ID=$Competitors[0]['ID']; 
        foreach($Competitors as $Competitor){
            if($ID!=$Competitor['ID']){
                DataBaseClass::Query("Update CommandCompetitor set Competitor=$ID where Competitor=".$Competitor['ID']);
                DataBaseClass::Query("Delete from Competitor where ID=".$Competitor['ID']);
            }

        }
    }
}


Function CompetitorReplace($user){
   
    if(isset($user->email)){
        $email=$user->email;
    }else{
        $email=false;
    }
    
        $name=short_Name(DataBaseClass::Escape($user->name));
        if(isset($user->wca_id)){
            $wcaid=$user->wca_id;
        }elseif(isset($user->wcaid)){
            $wcaid=$user->wcaid;    
        }else{
            $wcaid='';
        }
        
        if(!isset($user->id) or !is_numeric($user->id)){
            $wid=false;
        }else{
            $wid=$user->id;
        }
        if(isset($user->country_iso2)){
            $country=$user->country_iso2;
        }elseif(isset($user->region)){
            DataBaseClass::Query("Select ISO2 from Country where Name='".$user->region."'");
            $row=DataBaseClass::getRow();
            if(isset($row['ISO2'])){
                $country=$row['ISO2'];
            }else{
                $country='';    
            }
        }else{
            $country='';
        }
        $Language=false;
    
        if($wid){
            DataBaseClass::Query("Update Delegate set Name='$name',WCA_ID='$wcaid' where WID=$wid");    
            
            DataBaseClass::FromTable("Competitor","WID='$wid'");
            foreach(DataBaseClass::QueryGenerate() as $competitor_row){
                DataBaseClass::Query("Update Competitor set Name='$name',WCAID='$wcaid',Country='$country' ".($email?",Email='$email'":"").",WID=$wid,UpdateTimestamp=now() where ID=".$competitor_row['Competitor_ID']);    
                $Language=$competitor_row['Competitor_Language'];
            }
        }
        
        if($wcaid){
            DataBaseClass::Query("Update Delegate set Name='$name',WID='$wid' where WCA_ID='$wcaid'");    
        
            DataBaseClass::FromTable("Competitor","WCAID='$wcaid'");
            foreach(DataBaseClass::QueryGenerate() as $competitor_row){
                DataBaseClass::Query("Update Competitor set Name='$name',WCAID='$wcaid',Country='$country' ".($email?",Email='$email'":"").",WID=".($wid?$wid:'null').",UpdateTimestamp=now() where ID=".$competitor_row['Competitor_ID']);    
            }
        }
            
        
        Competitors_RemoveDuplicates();
        
        
        DataBaseClass::FromTable("Competitor");
        if($wid){
            DataBaseClass::Where("WID='$wid'");
        }else{
            DataBaseClass::Where("WID is null");
        }
        if($wcaid){
            DataBaseClass::Where("WCAID='$wcaid'");
        }else{
            DataBaseClass::Where("WCAID=''");
        }
        
        if(!$wid and !$wcaid){
            DataBaseClass::Where("Name='$name'");
        }
        $Competitor=DataBaseClass::QueryGenerate(false);
        
        if(!$Competitor){
            DataBaseClass::Query("Insert Into Competitor (WCAID,WID,Country,Name,Email) values ('$wcaid',".($wid?$wid:'null').",'$country','$name','$email')");    
            $CompetitorID=DataBaseClass::getID();
        }else{
            $CompetitorID=$Competitor['Competitor_ID'];
            CommandUpdateCompetitor($CompetitorID);
        }
        
        return $CompetitorID;
}