<?php


function date_range($start,$end=''){
    if(!$end)$end=$start;
    if(sizeof(explode("-",$start))!=3 or sizeof(explode("-",$end))!=3 ){
        return '-';
    }
    
    list($ys,$ms,$ds)=explode("-",$start);
    list($ye,$me,$de)=explode("-",$end);
    $Month= json_decode(ml('Month',[],false),true);
    if($ys!=$ye){
        return "{$Month[$ms]} $ds, $ys - {$Month[$me]} $de, $ye";    
    }else{
        if($ms!=$me){    
            return "{$Month[$ms]} $ds - {$Month[$me]} $de, $ys";    
        }else{
            if($ds!=$de){    
                return "{$Month[$ms]} $ds - $de, $ys";    
            }else{
                return "{$Month[$ms]} $ds, $ys";    
            }
        }
    }
    
    
    //$ss="{$Month[$ms]} $ds"
    
    //return "$ss, $ys";
    

    
}

function Competitor_Interval($Competition_Date){
    $str=substr($Competition_Date,-4).'-';
    $Month= json_decode(ml('Month',[],false),true);
    $str.=$Month[substr($Competition_Date,0,3)].'-'; 
    $str.=substr('0'.trim(substr($Competition_Date,4,2)),-2);    
    $date = date_create($str);
    
    $interval = date_diff($date, date_create(date('Y-m-d')))->format('%R%a');
            
    return $interval->format('%R%a');
}


function Competitor_Date_Start($Competition_Date){
    $str=substr($Competition_Date,-4).'-';
    $Month= json_decode(ml('Month',[],false),true);
    $str.=$Month[substr($Competition_Date,0,3)].'-'; 
    $str.=substr('0'.trim(substr($Competition_Date,4,2)),-2);    
          
    return $str;
}
    
function UpdateLocalID($competition){

    DataBaseClass::Query("select E1.ID,count(*)-1 LocalID from  Event E1 join Event E2 on E2.ID<=E1.ID and E1.Competition=E2.Competition
    where E1.Competition=$competition
    group by E1.ID");

    $LocalIDs=DataBaseClass::getRows();


    foreach($LocalIDs as $row){
      DataBaseClass::Query("update Event set LocalID='".$row['LocalID']."' where ID='".$row['ID']."'");
    }
    
 }


function CompetitionCompetitorsLoad($ID,$WCA,$Name,$type){
    $start=date('H:i:s'); 
    $registrations_data = file_get_contents(GetIni('WCA_API','competition')."/$WCA/registrations", false); 
    $registrations=json_decode($registrations_data);
    if($registrations){    
        foreach($registrations as $registration){
            DataBaseClass::FromTable('Competitor',"WID='{$registration->user_id}'");
            $Competitor=DataBaseClass::QueryGenerate(false);
            if(isset($Competitor['Competitor_ID'])){
                $Competitor_ID=$Competitor['Competitor_ID'];
            }else{
                $user_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/users/".$registration->user_id, false);   
                $user=json_decode($user_content);
                $Competitor_ID=CompetitorReplace($user->user);
            }
            DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
        }
    }

    $competitors_data = file_get_contents(GetIni('WCA_API','competition')."/$WCA/competitors", false); 
    $competitors=json_decode($competitors_data);
    if($competitors){    
        foreach($competitors as $competitor){
            DataBaseClass::FromTable('Competitor',"WCAID='{$competitor->wca_id}'");
            $Competitor=DataBaseClass::QueryGenerate(false);
            if(isset($Competitor['Competitor_ID'])){
                $Competitor_ID=$Competitor['Competitor_ID'];
            }else{
                $Competitor_ID=CompetitorReplace($competitor);
            }

            DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
        }
    }

    if(sizeof($competitors)){
        $str=sizeof($registrations)." / ".sizeof($competitors);
    }else{
        $str=sizeof($registrations);
    }
    $end=date('H:i:s');
    DataBaseClass::Query("Update Competition set LoadDateTime=concat(current_timestamp,' &#9642; ','$str') where ID='$ID'");
    AddLog('LoadRegistrations', $type,"$Name ($str) $start - $end ");
 }