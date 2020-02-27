<?php
CheckPostIsset('ID','Secret','Attempt','Value');
CheckPostNotEmpty('ID','Secret','Attempt');
CheckPostIsNumeric('ID','Attempt');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];
$Attempt=$_POST['Attempt'];
$Values=$_POST['Value'];

checkingScoreTakerCupAccess($ID,$Secret);

DataBaseClass::Query("Delete from `CupValue` where CupCell=$ID and Attempt=$Attempt");


if(
    !isset($Values[1][1]) or
    !isset($Values[1][2]) or
    !isset($Values[1][3]) or
    !isset($Values[2][1]) or
    !isset($Values[2][2]) or
    !isset($Values[2][3])
        ){
    HeaderExit();
}

foreach($Values as $command=>$command_values){
    foreach($command_values as $competitor=>$value){    
        if($value=='DNF'){
            $Values[$command][$competitor]=-1;
        }else{        
            $value=substr('00000'.str_replace(['.',':'],'',$value),-5,5);
            if(!is_numeric($value) or !$value){
                HeaderExit();
            }
            $minute=substr($value,0,1);
            $second=substr($value,1,2);
            $milisecond=substr($value,3,2);
            
            $Values[$command][$competitor]=$minute*60*100+$second*100+$milisecond;
        }
    }
}

if($Values[1][1]==-1 or $Values[1][2]==-1 or $Values[1][3]==-1){
    $Sum1=-1;
}else{
    $Sum1=$Values[1][1]+$Values[1][2]+$Values[1][3];    
}

if($Values[2][1]==-1 or $Values[2][2]==-1 or $Values[2][3]==-1){
    $Sum2=-1;
}else{
    $Sum2=$Values[2][1]+$Values[2][2]+$Values[2][3];    
}

$Point1=($Sum1!=-1 and ($Sum1<$Sum2 or $Sum2==-1))+0;
$Point2=($Sum2!=-1 and ($Sum2<$Sum1 or $Sum1==-1))+0;

DataBaseClass::Query("Insert into CupValue (CupCell,Value1_1,Value2_1,Value3_1,Sum1,Value1_2,Value2_2,Value3_2,Sum2,Attempt,Point1,Point2)"
        . " values ($ID,{$Values[1][1]},{$Values[1][2]},{$Values[1][3]},$Sum1,{$Values[2][1]},{$Values[2][2]},{$Values[2][3]},$Sum2,$Attempt,$Point1,$Point2)");

        DataBaseClass::Query("Update `CupCell` CC join `CupCell` CC_prev on CC_prev.ID=CC.CupCell1 or CC_prev.ID=CC.CupCell2 "
            . "set CC_prev.Status='fix' where CC.ID=$ID and CC_prev.Status not in('skip','blank')");
        
SetMessage(); 
header('Location: '.$_SERVER['HTTP_REFERER'].'#'.$ID);
exit();  
