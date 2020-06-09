<?php

$requests = getRequest();
if (!isset($requests[2]) or ! is_numeric($requests[2])) {
    echo 'Wrong competition ID';
    exit();
} else {
    $ID = $requests[2];
}

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $ID);


@$pdf = new FPDF('L', 'mm');
$pdf->SetTextColor(33, 33, 33);

DataBaseClass::FromTable("Competition");
DataBaseClass::Where_current("ID=$ID");
$competition = DataBaseClass::QueryGenerate(false);
DataBaseClass::Join_current("Event");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current("Discipline");
DataBaseClass::Join("DisciplineFormat", "Format");
DataBaseClass::Select("D.Name,E.ID,F.Result,D.Code,D.CodeScript");
DataBaseClass::OrderClear("Event", "Round desc");
DataBaseClass::Order("Discipline", "Name");

$disciplines = [];
foreach (DataBaseClass::QueryGenerate() as $discipline) {
    if (!isset($disciplines[$discipline['Code']])) {
        $disciplines[$discipline['Code']] = $discipline;
    }
}
$disciplines = array_values($disciplines);

if (!sizeof($disciplines)) {
    echo 'Events not found';
    exit();
}

$pdf->AddPage();


$pdf->SetFont('Arial', '', 24);
$str = iconv('utf-8', 'cp1252//TRANSLIT', $competition['Competition_Name']);
$pdf->Text(10, 14, $str);

$Y_header = 20;

$DY = min(($pdf->h - $Y_header - 15) / sizeof($disciplines), 30);


foreach ($disciplines as $n => $discipline) {
    $sy = $DY * $n + $Y_header;
    $ey = $DY * ($n + 1) + $Y_header;
    #$pdf->line(5,$sy,$pdf->w-5,$sy);


    DataBaseClass::FromTable("Event");
    DataBaseClass::Join_current("Command");
    DataBaseClass::Where_current("Place between 1 and 3");
    //DataBaseClass::Join_current("Competitor");
    DataBaseClass::Where("Event", "ID=" . $discipline['ID']);
    DataBaseClass::Join("Command", "Attempt");
    DataBaseClass::Join("Command", "CommandCompetitor");
    DataBaseClass::Join_current("Competitor");
    DataBaseClass::Where("Attempt", "Special='" . $discipline['Result'] . "'");
    DataBaseClass::Where("Attempt", "IsDNF=0");
    DataBaseClass::Select("Com.ID,Com.Place,A.vOut,Cm.Name");
    DataBaseClass::OrderClear("Command", "Place");
    $competitors = [];
    foreach (DataBaseClass::QueryGenerate() as $row) {
        if (!isset($competitors[$row['ID']])) {
            $competitors[$row['ID']] = $row;
        } else {
            $competitors[$row['ID']]['Name'] .= (', ' . $row['Name']);
        }
    }

    $competitors = array_values($competitors);


    //$competitors=DataBaseClass::QueryGenerate();


    $place_y = $DY / (max(array(sizeof($competitors), 3)) + 1) * 0.9;
    $font_size = min(array($place_y * 2, 14));

    if (sizeof($competitors)) {
        $pdf->SetFont('Arial', 'B', $font_size);
        $pdf->Text(10, $sy + $place_y, $discipline['Name']);
//            $pdf->SetFont('msserif','',min(array($place_y*2,10)));
//            $text = iconv('utf-8', 'windows-1251',$discipline['Name']);
//            $pdf->Text($X_left, $sy+$place_y*1,$text);   

        foreach ($competitors as $n => $competitor) {
            $pdf->SetFont('Arial', 'B', $font_size);
            $pdf->Text(15, $sy + $place_y * ($n + 2), $competitor['Place']);
            $pdf->SetFont('Arial', '', $font_size);
            $pdf->Text(20, $sy + $place_y * ($n + 2), sprintf("%10s", $competitor['vOut']));
            $pdf->Text(50, $sy + $place_y * ($n + 2), iconv('utf-8', 'cp1252//TRANSLIT', $competitor['Name']));
        }
    }
}

$pdf->Output($competition['Competition_WCA'] . '_' . 'Pedestal' . ".pdf", 'I');
$pdf->Close();
exit();
