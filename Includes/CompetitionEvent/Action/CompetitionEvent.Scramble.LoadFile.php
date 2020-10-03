<?php

$scrs = json_decode(file_get_contents($_FILES['file']['tmp_name']), true);

$see_option = json_decode($_POST['see_option'], false);
$wca_options = json_decode($_POST['wca_options'], false);
$data_tnoodle = json_decode($_POST['data_tnoodle'], false);

$Scrambles_row = array();
foreach ($scrs['wcif']['events'] as $event) {
    foreach($event['rounds'][0]['scrambleSets'] as $sets){
    foreach ($sets['scrambles'] as $scr) {
        $Scrambles_row[] = $scr;
    }
    foreach ($sets['extraScrambles'] as $scr) {
        $Scrambles_row[] = $scr;
    }
    }
}

#DataBaseClass::FromTable("Event", "ID=$ID");
#$row = DataBaseClass::QueryGenerate(false);

if (isset($row['Event_Competition'])) {
    $Competition = $row['Event_Competition'];
} else {
    $Competition = -1;
}
RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $Competition);


#Databaseclass::FromTable('Event', "ID='$ID'");
#Databaseclass::Join_current('DisciplineFormat');
#Databaseclass::Join_current('Discipline');
#Databaseclass::Join('DisciplineFormat', 'Format');
#Databaseclass::Join('Event', 'Competition');
#$data = Databaseclass::QueryGenerate(false);
#$Discipline = $data['Discipline_Code'];

$r = 0;

if ($see_option->code_script == 'pyra222') {
    foreach ($Scrambles_row as $n => $s) {
        $tmp = $s;
        $tmp = str_replace("R2", "r2", $tmp);
        $tmp = str_replace("R'", "r", $tmp);
        $tmp = str_replace("R", "r'", $tmp);
        $tmp = str_replace("r", "R", $tmp);

        $tmp = str_replace("U2", "u2", $tmp);
        $tmp = str_replace("U'", "u", $tmp);
        $tmp = str_replace("U", "u'", $tmp);
        $tmp = str_replace("u", "U", $tmp);

        $tmp = str_replace("F2", "B2", $tmp);
        $tmp = str_replace("F'", "B", $tmp);
        $tmp = str_replace("F", "B'", $tmp);
        $Scrambles_row[$n] = $tmp;
    }
}

DeleteScramble($see_option->id);

$scrambles = [];

if ($see_option->cup) {
    for ($g = 0; $g < $see_option->teams; $g++) {
        for ($a = 1; $a <= $see_option->attemption * $see_option->team_persons; $a++) {
            if (isset($Scrambles_row[$r])) {
                $scramble = $Scrambles_row[$r];
                $scramble = str_replace("\n", "", $scramble);
                $scrambles[$g][$a] = $scramble;
                $scramble = DataBaseClass::Escape($scramble);
                DataBaseClass::Query("Insert into Scramble (`Event`,`Scramble`,`Group`,`Attempt`) values ($see_option->id,'$scramble',1,$a) ");
            }
            $r++;
        }
    }

    DataBaseClass::Query("Update Event set scrambles = '" . DataBaseClass::Escape(json_encode($scrambles)) . "' WHERE ID = $see_option->id ");
    SetMessage();
    header('Location: ' . PageAction('CompetitionEvent.Scramble.Print') . "/" . $see_option->id);
    exit();
}

for ($g = 0; $g < $see_option->groups; $g++) {
    for ($a = 1; $a <= $see_option->attemption + $see_option->extra; $a++) {
        if (isset($Scrambles_row[$r])) {
            $scramble = $Scrambles_row[$r];
            $scramble = str_replace("\n", "", $scramble);
            $scrambles[$g][$a] = $scramble;
            $scramble = DataBaseClass::Escape($scramble);
            DataBaseClass::Query("Insert into Scramble (`Event`,`Scramble`,`Group`,`Attempt`) values ($see_option->id,'$scramble',$g,$a) ");
        }
        $r++;
    }
}

DataBaseClass::Query("Update Event set scrambles = '" . DataBaseClass::Escape(json_encode($scrambles)) . "' WHERE ID = $see_option->id ");


SetMessage();
header('Location: ' . PageAction('CompetitionEvent.Scramble.Print') . "/" . $see_option->id);
exit();
