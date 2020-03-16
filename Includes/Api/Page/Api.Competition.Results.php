<?php
header('Content-Type: application/json; charset=utf-8');
$Competition= ObjectClass::getObject('PageCompetition');


DataBaseClass::FromTable('Competition',"ID=".$Competition['Competition_ID']);
DataBaseClass::Join_current('Event');
DataBaseClass::Join_current("DisciplineFormat");
DataBaseClass::Join_current('Discipline');

$events=DataBaseClass::QueryGenerate();

DataBaseClass::Join('Event','Command');
DataBaseClass::Join('DisciplineFormat','Format');
DataBaseClass::OrderSpecial('D.Code, E.Round, case when Com.Place=0 then 999 else Com.Place end ');


$rounds=[];
foreach($events as $event){
    $rounds[$event['Discipline_Code']][]=$event['Event_Round'];
}
foreach($rounds as $code=>$values){
    $rounds[$code]=max($rounds[$code]);
}

$data=[];
foreach (DataBaseClass::QueryGenerate() as $command){
    $command_row=[];
    $command_row['id']=$command['Command_ID']+0;
    $command_row['competition_id']=$command['Competition_WCA'];
    $command_row['pos']=$command['Command_Place']+0;
    $command_row['event_id']=$command['Discipline_Code'];
    $Round=$command['Event_Round'];
    if($Round==$rounds[$command['Discipline_Code']]){
        $Round='f';
    }
    
    if($command['Event_CutoffMinute']+$command['Event_CutoffSecond']==0){
        $command_row['round_type_id']=$Round;
    }else{
        $command_row['round_type_id']= str_replace(['f','1','2','3'],['c','d','e','g'],$Round);
    } #[%w(c f), %w(d 1), %w(e 2), %w(g 3)]
    $command_row['format_id']=str_replace(['Ao5','Bo1','Bo2','Bo3','Mo3','Sum11'],['a','1','2','3','m','s'],$command['Format_Name']);
    
    DataBaseClass::FromTable('Command',"ID=".$command['Command_ID']);
    DataBaseClass::Join_current('CommandCompetitor');
    DataBaseClass::Join_current('Competitor');
    foreach(DataBaseClass::QueryGenerate() as $competitor){
        $command_row['team'][]=['name'=>$competitor['Competitor_Name'],'wca_id'=>$competitor['Competitor_WCAID'],'user_id'=>$competitor['Competitor_WID']];
    }
    
    DataBaseClass::FromTable('Attempt',"Command=".$command['Command_ID']);
    DataBaseClass::OrderClear('Attempt', 'Attempt');
    $attempts=DataBaseClass::QueryGenerate();
    $special=[];
    $attempts_format=[];
    foreach($attempts as $attempt){
        $attempt_format=0;
        if($attempt['Attempt_IsDNF']){
            $attempt_format=-1;
        }elseif($attempt['Attempt_IsDNS']){
            $attempt_format=-2;
        }else{
            $attempt_format=$attempt['Attempt_Minute']*60*100+$attempt['Attempt_Second']*100+$attempt['Attempt_Milisecond'];
        }
        
        
        if(!$attempt['Attempt_Special']){
            $attempts_format[$attempt['Attempt_Attempt']]=$attempt_format;
        }else{
           $special[$attempt['Attempt_Special']]=$attempt_format;
        }
    }
    
    
    for($i=1;$i<=$command['Format_Attemption'];$i++){
        if(isset($attempts_format[$i])){
            $command_row['attempts'][]=$attempts_format[$i];
        }else{
            $command_row['attempts'][]=0;
        }
    }
    
    foreach($special as $name=>$value){
        $command_row[str_replace(['Best','Average','Mean','Sum'],['best','average','average','best'],$name)]=$value;
    }
    if(!isset($special['Average']) and !isset($special['Mean'])){
        $command_row['average']=0;
    }
    
    $data[]=$command_row;
}
echo str_replace("    ","  ",json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));