<?php
$request= getRequest();
$EventCode=$request[1];

$Secret=false;
if(!is_numeric($EventCode)){
    DataBaseClass::Query("Select Event from ScramblePdf where Secret='$EventCode'");
    $row=DataBaseClass::getRow();
    if(isset($row['Event'])){
        $Secret=$EventCode;
        $EventCode=$row['Event'];
    }    
}

DataBaseClass::FromTable('Event', "ID='$EventCode'");
DataBaseClass::Join_current('Competition');
DataBaseClass::Join('Event','DisciplineFormat');
DataBaseClass::Join_current('Discipline');

$CompetitionEvent = DataBaseClass::QueryGenerate(false);
$title=$CompetitionEvent['Competition_WCA'].'_'.$CompetitionEvent['Discipline_Code'].'_'.$CompetitionEvent['Event_Round'];

$filename="Image/Scramble/".($Secret?$Secret:$CompetitionEvent['Event_ScrambleSalt']).".pdf";
if(file_exists($filename)){
    header('Content-Type: application/pdf');
    header('Content-Disposition:inline;filename="'.$title.'"');
    echo file_get_contents($filename);
}else{ ?>
    Scramble is not exists
<?php }
exit();