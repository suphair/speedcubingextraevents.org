<?php IncludeClass::Page('Index.Head') ?>
<?php
$requests = getRequest();
if (!isset($requests[2]) or!is_numeric($requests[2])) {
    echo 'Wrong event ID';
    exit();
} else {
    $ID = $requests[2];
}

DataBaseClass::FromTable("Event", "ID = $ID");
$row = DataBaseClass::QueryGenerate(false);

if (isset($row['Event_Competition'])) {
    $Competition = $row['Event_Competition'];
} else {
    $Competition = -1;
}

RequestClass::CheckAccessExit(__FILE__, "Competition.Settings", $Competition);

Databaseclass::FromTable('Event', "ID='$ID'");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('DisciplineFormat', 'Format');
Databaseclass::Join('Event', 'Competition');
$data = Databaseclass::QueryGenerate(false);
$Competition_WCA = str_replace('.', '_', $data['Competition_WCA']);
$FileName = $Competition_WCA . "_" . $data['Discipline_Code'] . "_" . $data['Event_Round'];
if (!$data['Discipline_GlueScrambles'] or!$data['Discipline_TNoodle']) {
    exit();
}
?>
<head>
    <title><?= $data['Discipline_Name'] ?><?= $data['Event_vRound'] ?></title>
</head>
<h1><?= $data['Competition_Name'] ?> ▪ <?= $data['Discipline_Name'] ?><?= $data['Event_vRound'] ?></h1>
<?php
$format = 'PDF';
include 'CompetitionEvent.Scramble.Main.php';

exit();
