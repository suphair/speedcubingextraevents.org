<?php
CheckPostIsset('Competitor','Competition','WCAID');
CheckPostNotEmpty('Competitor','Competition','WCAID');
CheckPostIsNumeric('Competitor','Competition');
$Competitor=$_POST['Competitor'];
$Competition=$_POST['Competition'];
$WCAID= DataBaseClass::Escape($_POST['WCAID']);

RequestClass::CheckAccessExit(__FILE__, "Competition.Event.Settings",$Competition);

DataBaseClass::FromTable('Command');
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Competitor');
DataBaseClass::Where_current("ID='$Competitor'");
DataBaseClass::Join('Command','Event');
DataBaseClass::Join_current('Competition');
DataBaseClass::Where_current("ID='$Competition'");

$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Competitor_ID'])){
    DataBaseClass::Query("Select ID,Name from  Competitor where WCAID='$WCAID'");
    $row2=DataBaseClass::getRow();
    if(isset($row2['ID'])){
        DataBaseClass::Query("update CommandCompetitor set Competitor='".$row2['ID']."' where Competitor='".$Competitor."'");
        LogsRegistration($row['Event_ID'], 'D !', $row['Competitor_Name'].' '. $row['Competitor_ID'].' -> '. $row2['Name'].' '. $row2['ID'].' '. $WCAID);
    }        
}


header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  

