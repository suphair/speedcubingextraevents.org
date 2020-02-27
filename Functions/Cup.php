<?php

function availableCupChange($eventID)
{    
    DataBaseClass::Query("Select * from CupCell CC where CC.Event='".$eventID."'");
    return DataBaseClass::getAffectedRows()==0;
}

function availableCupReset($eventID)
{    
    DataBaseClass::Query("Select * from CupValue CV join CupCell CC on CC.ID=CV.CupCell  where CC.Event='".$eventID."'");
    if(DataBaseClass::getAffectedRows()) return false;
    DataBaseClass::Query("Select * from CupCell CC where CC.Status in('done','fix') and CC.Event='".$eventID."'");
    if(DataBaseClass::getAffectedRows()) return false;
    return true;
}

function availableCupDistribution($eventID){
    DataBaseClass::Query("Select CommandsCup from Event where ID='".$eventID."'");
    $CommandsCup=json_decode(DataBaseClass::getRow()['CommandsCup'],true);
    if(!isset($CommandsCup['Count']))return false;
    $Count=$CommandsCup['Count'];
    
    DataBaseClass::Query("Select * from Command where Event='".$eventID."' and inCup");
    return DataBaseClass::getAffectedRows()<=$Count;
}

function CupDistribution($ID,$Type){
    $cells=[];
    DataBaseClass::Query("Select CommandsCup from Event where ID='".$ID."'");
    $CommandsCup=json_decode(DataBaseClass::getRow()['CommandsCup'],true);
    $Count=$CommandsCup['Count'];
    $Rounds=$CommandsCup['Round'];
    
    $default[32]=[1,32,16,17,8,25,9,24,4,29,13,20,5,28,12,21,2,31,15,18,7,26,10,23,3,30,14,19,6,27,11,22];
    $default[16]=[];
    $default[8]=[];
    $default[4]=[];
    $default[2]=[];
    for($i=0;$i<32/2;$i++){
      $default[16][]=min([$default[32][$i*2],$default[32][$i*2+1]]);
    }
    for($i=0;$i<16/2;$i++){
      $default[8][]=min([$default[16][$i*2],$default[16][$i*2+1]]);
    }
    for($i=0;$i<8/2;$i++){
      $default[4][]=min([$default[8][$i*2],$default[8][$i*2+1]]);
    }
    for($i=0;$i<4/2;$i++){
      $default[2][]=min([$default[4][$i*2],$default[4][$i*2+1]]);
    }

    $distribution=$default[$Count];
    $commands=[];
    $sort=" ";
    if($Type=='default'){
        $sort=" order by CardID";    
    }
    if($Type=='random'){
        $sort=" order by rand()";    
    }
    if($Type=='name'){
        $sort=" order by Name";    
    }
    
    DataBaseClass::Query("Select * from Command where Event=$ID and inCup ".$sort);
    foreach(DataBaseClass::getRows() as $row){
        $commands[]=$row;
    }
    for($round=1;$round<=$Rounds;$round++){
        for($i=1;$i<=$Count/pow(2,$round);$i++){ 
            
            if($round==1){
                if(isset($distribution[$i*2-2]) and isset($commands[$distribution[$i*2-2]-1])){
                    $command1=$commands[$distribution[$i*2-2]-1]['ID'];
                }else{
                    $command1=false+0;
                }

                if(isset($distribution[$i*2-1]) and isset($commands[$distribution[$i*2-1]-1])){
                    $command2=$commands[$distribution[$i*2-1]-1]['ID'];
                }else{
                    $command2=false+0;
                }
            }else{
                $command1=$cells[$round-1][$i*2-1]['CommandWin'];
                $command2=$cells[$round-1][$i*2]['CommandWin'];
            }
            $commandwin=false+0;

            $blank=false;
            if($cells[$round-1][$i*2-1]['Status']!='run' and $cells[$round-1][$i*2]['Status']!='run'){
                if(!$command2){
                    $commandwin=$command1;
                    if(!$command1){
                        $blank=true;
                        $commandwin=false+0;
                    }
                }
            }
            
            
            $commandWin[$round][$i]=$commandwin;
            if($blank){
                $status='blank';
            }else{
                if($commandwin){
                    $status='skip';    
                }else{
                    $status='run';    
                }
            }
            if($round>1){
                if($cells[$round-1][$i*2-1]['Status']=='run' or $cells[$round-1][$i*2]['Status']=='run'
                        or $cells[$round-1][$i*2-1]['Status']=='wait' or $cells[$round-1][$i*2]['Status']=='wait'){
                    $status='wait';    
                }
            }
            
            if($round==1){
                DataBaseClass::Query("Insert into CupCell"
                    . " (Event,Command1,Command2,CommandWin,Round,Number,Status) values "
                    . " (".$ID.",$command1,$command2,$commandwin,$round,$i,'$status') ");
            }else{
                DataBaseClass::Query("Insert into CupCell"
                    . " (Event,Command1,Command2,CommandWin,Round,Number,Status,CupCell1,CupCell2) values "
                    . " (".$ID.",$command1,$command2,$commandwin,$round,$i,'$status',{$cells[$round-1][$i*2-1]['ID']},{$cells[$round-1][$i*2]['ID']}) ");
            }

            $cells[$round][$i]=['ID'=>DataBaseClass::getID(),'CommandWin'=>$commandwin,'Status'=>$status];
        }
    }
}