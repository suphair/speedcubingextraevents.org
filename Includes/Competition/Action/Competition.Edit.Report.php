<?php

CheckPostIsset('ID', 'Report');
CheckPostNotEmpty('ID');
CheckPostIsnumeric('ID');

$ID = $_POST['ID'];
RequestClass::CheckAccessExit(__FILE__, 'Competition.Report.Create', $ID);
$Report = DataBaseClass::Escape($_POST['Report']);


$Report_ID = false;
if (getDelegate()) {
    $Delegate = getDelegate()['Delegate_ID'];
    $DelegateWCA = 'null';
    DataBaseClass::Query("Select ID from CompetitionReport  where Competition=$ID and Delegate=$Delegate");
    $row = DataBaseClass::getRow();
    if (isset($row['ID'])) {
        $Report_ID = $row['ID'];
    }
} else {
    $DelegateWCA = getCompetitor()->id;
    $Delegate = 'null';
    DataBaseClass::Query("Select ID from CompetitionReport  where Competition=$ID and DelegateWCA=$DelegateWCA");
    $row = DataBaseClass::getRow();
    if (isset($row['ID'])) {
        $Report_ID = $row['ID'];
    }
}

$Parsedown = isset($_POST['Parsedown']) + 0;

if (!trim($Report)) {
    DataBaseClass::Query("Delete from `CompetitionReport` where ID=$Report_ID");
} else {
    if ($Report_ID) {
        DataBaseClass::Query("Update `CompetitionReport` set Report='$Report',Parsedown=$Parsedown where ID=$Report_ID");
    } else {
        DataBaseClass::Query("insert into `CompetitionReport` (Report,Competition,Delegate,DelegateWCA,Parsedown) values ('$Report',$ID,$Delegate,$DelegateWCA,$Parsedown)");

        DataBaseClass::FromTable("Competition", "ID=" . $ID);
        $Competition = DataBaseClass::QueryGenerate(false);
        $Name = getCompetitor()->name;

        notification::put('report.new', 'SEE: New report ' . $Competition['Competition_Name'] . ' / ' . $Name, "<pre>" . $Competition['Competition_Name'] . " / " . $Name . "<br><a href='https://" . PageIndex() . "/Competition/" . $Competition['Competition_WCA'] . "/Report'>View report</a>");
    }
}


header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();

