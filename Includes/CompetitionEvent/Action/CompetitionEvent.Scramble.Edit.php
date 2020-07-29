<?php

CheckPostIsset('ID');
CheckPostIsNumeric('ID');
CheckPostNotEmpty('ID', 'Scrambles');
$ID = $_POST['ID'];
$Scrambles = $_POST['Scrambles'];

DataBaseClass::FromTable("Event", "ID=$ID");
$row = DataBaseClass::QueryGenerate(false);


if (isset($row['Event_Competition'])) {
    $Competition = $row['Event_Competition'];
} else {
    $Competition = -1;
}
RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $Competition);

$Scrambles_row = explode("\n", $Scrambles);

Databaseclass::FromTable('Event', "ID='$ID'");
Databaseclass::Join_current('DisciplineFormat');
Databaseclass::Join_current('Discipline');
Databaseclass::Join('DisciplineFormat', 'Format');
Databaseclass::Join('Event', 'Competition');
$data = Databaseclass::QueryGenerate(false);
$Discipline = $data['Discipline_Code'];
$r = 0;

DeleteScramble($ID);

$scrambles = [];

for ($g = 0; $g < $data['Event_Groups']; $g++) {
    for ($a = 1; $a <= $data['Format_Attemption'] + 2; $a++) {
        if (isset($Scrambles_row[$r])) {
            $scrambles[$g][$a] = $Scrambles_row[$r];
            $scramble = DataBaseClass::Escape($Scrambles_row[$r]);
            $scramble = str_replace('\r', "", $scramble);
            DataBaseClass::Query("Insert into Scramble (`Event`,`Scramble`,`Group`,`Attempt`) values ($ID,'$scramble',$g,$a) ");
        }
        $r++;
    }
}
DataBaseClass::Query("UPDATE Event SET scrambles='" . DataBaseClass::Escape(json_encode($scrambles)) . "' WHERE ID = $ID");

SetMessage();
header('Location: ' . PageAction('CompetitionEvent.Scramble.Print') . "/$ID");
exit();
