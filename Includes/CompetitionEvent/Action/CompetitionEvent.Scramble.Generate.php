<?php

$request = Request();
if (!isset($request[2]) or!is_numeric($request[2])) {
    exit();
}
$ID = $request[2];

DataBaseClass::FromTable("Event", "ID=$ID");
$row = DataBaseClass::QueryGenerate(false);


if (isset($row['Event_Competition'])) {
    $Competition = $row['Event_Competition'];
} else {
    $Competition = -1;
}

RequestClass::CheckAccessExit(__FILE__, "Competition.Settings", $Competition);

DeleteScramble($ID);

DataBaseClass::FromTable('Event', "ID=$ID");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('DisciplineFormat', 'Format');
$data = DataBaseClass::QueryGenerate(false);
$Discipline = $data['Discipline_Code'];
$CodeScript = $data['Discipline_CodeScript'];
$Attemption = $data['Format_Attemption'];

$exs = 2;
if ($Attemption < 5) {
    $exs = 1;
}

$scrambles = [];

for ($A = 1; $A <= $Attemption + $exs; $A++) {
    for ($I = 0; $I < $data['Event_Groups']; $I++) {
        $scramble = GenerateScramble($CodeScript);
        if ($scramble) {
            $scrambles[$I][$A] = $scramble;
            $scramble = DataBaseClass::Escape($scramble);
            DataBaseClass::Query("Insert into Scramble (`Event`,`Scramble`,`Group`,`Attempt`) values ($ID,'$scramble',$I,$A) ");
        }
    }
}

DataBaseClass::Query("UPDATE Event SET scrambles='" . DataBaseClass::Escape(json_encode($scrambles)) . "' WHERE ID = $ID");

SetMessage();
$date = filter_input(INPUT_GET, 'date');
header('Location: ' . PageAction('CompetitionEvent.Scramble.Print') . "/$ID/?date=$date");
exit();
