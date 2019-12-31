<?php
CheckPostIsset('Competition','Events');
CheckPostNotEmpty('Competition','Events');
CheckPostIsNumeric('Competition');
$Competition=$_POST['Competition'];

RequestClass::CheckAccessExit(__FILE__, "Competition.Settings",$Competition);
$Events=[];
$error='';
foreach($_POST['Events'] as $row){
   $arr= json_decode($row);
   if(!is_array($arr) or sizeof($arr)!=2 or !is_numeric($arr[0]) or!is_numeric($arr[1]) or !in_array($arr[1],[1,2,3,4])){
       $error.="$row is wrong . ";
   }else{
       $Events[]=$arr;
   }
}


foreach($Events as $row){
    $Event=$row[0];
    $round=$row[1];
    DataBaseClass::FromTable("DisciplineFormat","Discipline=".$Event);
    $EventFormat=DataBaseClass::QueryGenerate(false);
    if(is_array($EventFormat)){    
        DataBaseClass::Query("Select E.ID from `Event` E "
        . "join DisciplineFormat DF on E.DisciplineFormat=DF.ID where"
        . " E.`Competition`='$Competition' and DF.Discipline='$Event' and E.Round=$round");
        
        if(DataBaseClass::rowsCount()>0){
            $error.="$Event round $round exists. ";
        }else{
            DataBaseClass::Query("Insert into  `Event` "
                    . " (`Competition`,`DisciplineFormat`,`Secret`,`Round`)"
                    . " VALUES('$Competition','".$EventFormat['DisciplineFormat_ID']."','". random_string(16)."','$round')");
            EventRoundView($Competition);
            SetMessage();
            $EventID=DataBaseClass::getID();
        }

        UpdateLocalID($Competition);
    }
}

if($error){
    SetMessageName('CompetitionEvents.Add.Error', $error);
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
