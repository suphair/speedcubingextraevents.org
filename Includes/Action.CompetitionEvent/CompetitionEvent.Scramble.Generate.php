<?php
$request=Request();
if(!isset($request[2]) or !is_numeric($request[2])){
    exit();
}
$ID=$request[2];



if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
}else{
    $Competition=-1;
}

RequestClass::CheckAccessExit(__FILE__, "Competition.Settings",$Competition);

DeleteScramble($ID);

DataBaseClass::FromTable('Event',"ID=$ID");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('DisciplineFormat','Format');
$data=DataBaseClass::QueryGenerate(false);
$Discipline=$data['Discipline_Code'];
$CodeScript=$data['Discipline_CodeScript'];
$Attemption=$data['Format_Attemption'];

$exs=2;
if($CodeScript=='9x9' or $CodeScript=='8x8'){
        $exs=1;
}

for($A=1;$A<=$Attemption+$exs;$A++){
    for ($I=1;$I<=$data['Event_Groups'];$I++){
        $scramble=GenerateScramble($CodeScript);
        if($scramble){  
            $scramble=DataBaseClass::Escape($scramble);
            DataBaseClass::Query("Insert into Scramble (`Event`,`Scramble`,`Group`,`Attempt`) values ($ID,'$scramble',$I,$A) ");
        }
    }
}


SetMessage();
header('Location: '.PageAction('CompetitionEvent.Scramble.Print')."/$ID");
exit();  