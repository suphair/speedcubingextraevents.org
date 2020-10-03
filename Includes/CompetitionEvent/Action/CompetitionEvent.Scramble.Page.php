<head>
    <?php IncludeClass::Page('Index.Head') ?>
</head>
<?php
$requests = getRequest();
if (!isset($requests[2]) or!is_numeric($requests[2])) {
    echo 'Wrong event ID';
    exit();
} else {
    $ID = $requests[2];
}

DataBaseClass::FromTable("Event", "ID=$ID");
$row = DataBaseClass::QueryGenerate(false);


if (isset($row['Event_Competition'])) {
    $Competition = $row['Event_Competition'];
} else {
    $Competition = -1;
}

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $Competition);


DataBaseClass::Query("Select S.Timestamp, S.Scramble,S.Group,S.Attempt from `Scramble` S where S.`Event`='$ID' order by S.Group, S.Attempt");
$scrambles = array();
$scrambles_row = array();
foreach (DataBaseClass::getRows() as $row) {
    $scrambles[$row['Group']][$row['Attempt']] = array('Scramble' => $row['Scramble'], 'Timestamp' => $row['Timestamp']);
    $scrambles_row[] = $row['Scramble'];
}


Databaseclass::FromTable('Event', "ID='$ID'");
Databaseclass::Join_current('DisciplineFormat');
Databaseclass::Join_current('Discipline');
Databaseclass::Join('DisciplineFormat', 'Format');
Databaseclass::Join('Event', 'Competition');
$data = Databaseclass::QueryGenerate(false);
$Discipline = $data['Discipline_Code'];
$Attemption = $data['Format_Attemption'];
?>
<head>
    <title><?= $data['Discipline_Name'] ?><?= $data['Event_vRound'] ?></title>
</head>

<h1>
    <?= $data['Competition_Name'] ?>
    ▪
    <?= $data['Discipline_Name'] ?>
    <?= $data['Event_vRound'] ?>
</h1>

<?php
$format = 'JSON';
include 'CompetitionEvent.Scramble.Main.php';

exit();
