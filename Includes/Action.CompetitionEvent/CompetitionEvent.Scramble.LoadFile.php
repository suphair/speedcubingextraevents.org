<?php
$scrs=json_decode(file_get_contents($_FILES['file']['tmp_name']),true);
$Scrambles_row=array();
foreach($scrs['sheets'] as $sheet){
    foreach($sheet['scrambles'] as $scr){    
        $Scrambles_row[]=$scr;
    }
    foreach($sheet['extraScrambles'] as $scr){    
        $Scrambles_row[]=$scr;
    }
    
}

CheckPostIsset('ID');
CheckPostIsNumeric('ID');
CheckPostNotEmpty('ID');
$ID=$_POST['ID'];

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
}else{
    $Competition=-1;
}
RequestClass::CheckAccessExit(__FILE__,'Competition.Settings',$Competition);


Databaseclass::FromTable('Event', "ID='$ID'");
Databaseclass::Join_current('DisciplineFormat');
Databaseclass::Join_current('Discipline');
Databaseclass::Join('DisciplineFormat','Format');
Databaseclass::Join('Event','Competition');
$data=Databaseclass::QueryGenerate(false);
$Discipline=$data['Discipline_Code'];
$r=0;

if($data['Discipline_Code']=='Pyraminx2x2x2'){
    foreach($Scrambles_row as $n=>$s){
        $tmp=$s;
        $tmp=str_replace("R2","r2",$tmp);
        $tmp=str_replace("R'","r",$tmp);
        $tmp=str_replace("R","r'",$tmp);
        $tmp=str_replace("r","R",$tmp);
        
        $tmp=str_replace("U2","u2",$tmp);
        $tmp=str_replace("U'","u",$tmp);
        $tmp=str_replace("U","u'",$tmp);
        $tmp=str_replace("u","U",$tmp);
        
        $tmp=str_replace("F2","B2",$tmp);
        $tmp=str_replace("F'","B",$tmp);
        $tmp=str_replace("F","B'",$tmp);
        $Scrambles_row[$n]=$tmp;   
    } 
}


DeleteScramble($ID);

for($g=1;$g<=$data['Event_Groups'];$g++){
    for($a=1;$a<=$data['Format_Attemption']+2;$a++){
        if(isset($Scrambles_row[$r])){
            $scramble=$Scrambles_row[$r];
            $scramble=str_replace("\n","",$scramble);
            $scramble=DataBaseClass::Escape($scramble);
            DataBaseClass::Query("Insert into Scramble (`Event`,`Scramble`,`Group`,`Attempt`) values ($ID,'$scramble',$g,$a) ");
        }
        $r++;
    }
}

SetMessage();
header('Location: '.PageAction('CompetitionEvent.Scramble.Print')."/$ID");
exit();  

