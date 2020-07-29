<?php

function GenerateScramble($event, $training = false) {
    if ($training) {
        switch ($event) {
            case 'pyra222': return GenerateTraining_pyra222();
            case 'mirror': return GenerateTraining_mirror();
            case 'mirrorBLD': return GenerateTraining_mirrorBLD();
        }
    }

    switch ($event) {
        case '223': return Generate_223($training);
        case '332': return Generate_332();
        case '888': return GenerateNxN(8, 100, 10);
        case '999': return GenerateNxN(9, 100, 10);
        case 'fto': return Generate_fto();
        case 'ivy': return Generate_ivy();
        case 'kilo': return Generate_kilo($training);
        case 'redi':return Generate_redi();
        case 'dino':return Generate_dino();
        case 'sia113': return Generate_sia113();
        case 'pyra444': return Generate_pyra444();
        case 'curvycopter': return Generate_curvycopter($training);

        default: return false;
    }
}

function DeleteScramble($ID) {
    foreach (DataBaseClass::SelectTableRows('Scramble', "Event=$ID") as $scramble) {
        $filename = "Image/Scramble/" . $scramble['Scramble_ID'] . ".png";

        if (file_exists($filename)) {
            unlink($filename);
        }
        DataBaseClass::Query("Delete from `Scramble` where `ID`='" . $scramble['Scramble_ID'] . "' ");
    }
}
