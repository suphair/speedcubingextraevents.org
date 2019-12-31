<?php
$request= getRequest();
$CompetitionID=$request[1];

DataBaseClass::FromTable('Competition','ID='.$CompetitionID);
$Competition=DataBaseClass::QueryGenerate(false); 

$scrambles=[];
DataBaseClass::FromTable('Event');
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Where('Event','Competition='.$CompetitionID);
foreach(DataBaseClass::QueryGenerate() as $row){    
    $file="Image/Scramble/".$row['Event_ScrambleSalt'].".pdf";
    if (file_exists($file)){ 
        $scrambles[$row['Discipline_Code']."_".$row['Event_Round']]=$file;
    }
}


$zip = new ZipArchive();
$zip_name="Scramble/Zip/".$Competition['Competition_WCA'].".zip";
@unlink($zip_name);
$zip->open($zip_name, ZIPARCHIVE::CREATE);
foreach($scrambles as $name=>$scramble){
    $zip->addFile($scramble,$Competition['Competition_WCA']."_".$name.".pdf");
}

$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition:inline;filename="Scrambles_'.$Competition['Competition_WCA'].'"');
echo file_get_contents($zip_name);
exit();