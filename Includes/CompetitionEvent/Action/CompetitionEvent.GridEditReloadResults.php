<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
    RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$Competition);
    if(availableCupChange($ID)){
        SaveValue('GridEditReloadResults_'.$ID,date('d.m.Y H:i:s'));
        CommandUpdate($ID);
        DataBaseClass::Query("Select * from Command where Event=".$ID." order by Sum333, ID");
        $cardID=0;
        foreach(DataBaseClass::getRows() as $row){
            $cardID++;
            DataBaseClass::Query("Update Command set CardID=$cardID where ID={$row['ID']}");    
        }
    }
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();