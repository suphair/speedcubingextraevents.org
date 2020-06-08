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
    
    $registrations=getCompetitionRegistrationsWcaApi($WCA,'competitionCompetitorsLoad');
    if($registrations){    
        foreach($registrations as $registration){
            DataBaseClass::FromTable('Competitor',"WID='{$registration->user_id}'");
            $Competitor=DataBaseClass::QueryGenerate(false);
            if(isset($Competitor['Competitor_ID'])){
                $Competitor_ID=$Competitor['Competitor_ID'];
            }else{
                if($user=getUserWcaApi($registration->user_id, 'competitionCompetitorsLoad')){
                    $Competitor_ID=CompetitorReplace($user);
                }
            }
            DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
        }
    }
    
    $competitors=getCompetitionCompetitorsWcaApi($WCA,'competitionCompetitorsLoad');
    
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

    if($competitors and $registrations){
        $str = sizeof($registrations)." / ".sizeof($competitors);
    }elseif($registrations){
        $str = sizeof($registrations);
    }else{
        $str = false;
    }

    $end=date('H:i:s');
    DataBaseClass::Query("Update Competition set LoadDateTime=concat(current_timestamp,' &#9642; ','$str') where ID='$ID'");
    AddLog('LoadRegistrations', $type,"$Name ($str) $start - $end ");
 }
 
     
function CompetitionCompetitorsLoadCubingchina($ID,$Name,$type){
    $start=date('H:i:s'); 

    $Code=str_replace([' ',"'"],['-',''],$Name);
    $url="https://cubingchina.com/competition/".$Code;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEJAR, realpath('Cookies/cubingchina.com')); 
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Safari/605.1.15');
    curl_exec($ch);
    curl_close($ch);
    
    $url="https://cubingchina.com/api/v0/competition/".$Code."/competitors/?lang=en";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Safari/605.1.15');
    curl_setopt($ch, CURLOPT_COOKIEFILE,  realpath('Cookies/cubingchina.com')); 
    session_write_close();
    $data = curl_exec($ch);
    curl_close($ch);

    $competitors=json_decode($data);

    if($competitors){    
        foreach($competitors->data as $competitor){
            $wca=$competitor->competitor->wcaid;
            $name=$competitor->competitor->name;
            if($wca){
                DataBaseClass::FromTable('Competitor',"WCAID='$wca'");
            }else{
                DataBaseClass::FromTable('Competitor');
                DataBaseClass::Where_current("WCAID=''");
                DataBaseClass::Where_current("Name='$name'");
            }
            $Competitor=DataBaseClass::QueryGenerate(false);
            if(isset($Competitor['Competitor_ID'])){
                $Competitor_ID=$Competitor['Competitor_ID'];
            }else{
                $Competitor_ID=CompetitorReplace($competitor->competitor);
            }
            DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
        }
    }

    $end=date('H:i:s');
    DataBaseClass::Query("Update Competition set LoadDateTime=concat(current_timestamp,' &#9642; ','".sizeof($competitors->data)."') where ID='$ID'");
    AddLog('LoadRegistrations', $type,"$Name (".sizeof($competitors->data).") $start - $end ");
 }