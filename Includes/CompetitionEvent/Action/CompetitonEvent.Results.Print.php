<?php

$requests = getRequest();
if (!isset($requests[2]) or ! is_numeric($requests[2])) {
    echo 'Wrong event ID';
    exit();
} else {
    $ID = $requests[2];
}

DataBaseClass::Query("select  GROUP_CONCAT(C.Name order by C.Name SEPARATOR ', ') vName, case when Com.Place>0 then Com.Place else '' end Place, Com.ID, Com.Decline,Com.CardID "
        . " from `Command` Com join CommandCompetitor CC on CC.Command=Com.ID join Competitor C on C.ID=CC.Competitor "
        . " where Com.Event='$ID' and not Com.Decline "
        . " group by Com.ID "
        . "order by case when Com.Place>0 then Com.Place else 999 end, 1");
$commands = DataBaseClass::getRows();

DataBaseClass::Query("select E.vRound,F.Attemption, F.Result, F.ExtResult, C.Name Competition, D.Name Discipline, C.WCA Competition_WCA, D.Code Discipline_Code,D.CodeScript Discipline_CodeScript, D.Competitors Discipline_Competitors from `Format` F "
        . " join `DisciplineFormat` DF on DF.Format=F.ID "
        . " join `Discipline` D on DF.Discipline=D.ID "
        . " join `Event` E on E.DisciplineFormat = DF.ID "
        . " join Competition C on C.ID=E.Competition where E.ID='$ID'");
$data = DataBaseClass::getRow();

$column_attempt_count = $data['Attemption'] + 1;
if ($data['ExtResult']) {
    $column_attempt_count++;
}

$column_competitor_count = $data['Discipline_Competitors'];

$xPlace = 10;
if ($data['Attemption'] > 5) {
    $xAttempt = 18;
} else {
    $xAttempt = 25;
}
$xCompetitor = 50;

$pdf = new FPDF('L', 'mm');
$max_page = 20;
$pdf->SetTextColor(33, 33, 33);

$pages = ceil(sizeof($commands) / $max_page);

$xStart = 10;
$xEnd = $pdf->w - 10;

for ($p = 0; $p < $pages; $p++) {
    $start = $p * $max_page;
    $end = min(array(($p + 1) * $max_page, sizeof($commands)));
    $on_page = ($end - $start + 1);
    $pdf->AddPage();

    $pdf->SetLineWidth(1);
    //$pdf->Line($xStart, 35, $xEnd, 35);
    //$pdf->Line($xStart + $xPlace, 30, $xEnd - $xAttempt * $column_attempt_count, 30);

    $n = 0;
    for ($c = $start; $c < $end; $c++) {
        $command = $commands[$c];
        $n++;

        if ($c % 2 == 0) {
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Rect(10, 38 + ($n - 1) * 8, $pdf->w - 20, 8, "F");
        }
        $pdf->SetLineWidth(0.3);
        if ($n > 0) {
            //$pdf->Line(5, 38+($n-1)*8 , $pdf->w - 5, 38+($n-1)*8);
        }
        //$pdf->Line(5, 38+$n*8 , $pdf->w - 5, 38+$n*8);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(12, 35 + $n * 8, $command['Place']);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(23, 35 + $n * 8, iconv('utf-8', 'cp1252//TRANSLIT', $command['vName']));

        DataBaseClass::Query("select * from `Attempt` A where Command='" . $command['ID'] . "' ");
        $attempt_rows = DataBaseClass::getRows();
        $attempts = array();
        for ($i = 1; $i <= $data['Attemption']; $i++) {
            $attempts[$i] = "";
        }

        foreach (DataBaseClass::SelectTableRows("Format") as $format) {
            $attempts[$format['Format_Result']] = "";
            $attempts[$format['Format_ExtResult']] = "";
        }

        foreach ($attempt_rows as $attempt_row) {
            $attempt = $attempt_row['vOut'];
            if ($attempt_row['Except']) {
                $attempt = "($attempt)";
            }

            if ($attempt_row['Attempt']) {
                $attempts[$attempt_row['Attempt']] = $attempt;
            } else {
                $attempts[$attempt_row['Special']] = $attempt;
            }
        }

        $dX = 1;
        if ($data['ExtResult']) {
            $i = 5;
            $pdf->SetFont('Arial', '', 12);
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', $attempts[$data['ExtResult']]));
            $dX++;
        }

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', $attempts[$data['Result']]));
        $dX++;

        $pdf->SetFont('Arial', '', 12);
        for ($i = $data['Attemption'] - 1; $i >= 0; $i--) {
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', $attempts[$i + 1]));
            $dX++;
        }
    }

    //if(file_exists("Image/Competition/".$data['Competition_WCA'].'.jpg')){
    //    $pdf->Image("Image/Competition/".$data['Competition_WCA'].'.jpg',5,5,25,25,'jpg');
    //}
    #$pdf->Image("Logo/Logo_Black.png",$pdf->w-25,5,20,20,'png');


    $pdf->SetFont('Arial', '', 16);
    $str = iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition']);
    $pdf->Text(10, 13, $str);
    $pdf->SetFont('Arial', '', 24);
    $pdf->Text(10, 24, $data['Discipline'] . $data['vRound']);
    //$pdf->SetFont('Arial','',20);
    //$pdf->SetLineWidth(0.3);
    #$pdf->Line(5, 38 , $pdf->w - 5, 38);
    #$pdf->SetLineWidth(0.1);
    #$pdf->SetFont('Arial','',12);
    #$pdf->Text(6, 35, 'Place');
    #$pdf->Line(15, 30, 15,32+8*$on_page);
    #if($data['Discipline_Competitors']>1){
    #    $pdf->Text(18, 35, 'Team');    
    #}else{
    #    $pdf->Text(18, 35, 'Competitor');
    #}

    $dX = 1;
    $pdf->SetFont('Arial', 'B', 12);
    if ($data['ExtResult']) {
        $i = 5;
        #$pdf->Line($xEnd -  $dX * $xAttempt, 30, $xEnd -  $dX * $xAttempt, 32+8*$on_page);
        $pdf->Text($xEnd - $dX * $xAttempt - 1, 35, sprintf('%0 10s', $data['ExtResult']));
        $dX++;
    }
    #$pdf->Line($xEnd -  $dX * $xAttempt, 30, $xEnd -  $dX * $xAttempt, 32+8*$on_page);
    #$pdf->SetFont('Arial','',12);
    $pdf->Text($xEnd - $dX * $xAttempt - 1, 35, sprintf('%0 10s', $data['Result']));
    $dX++;
    #$pdf->SetFont('Arial','',12);
    for ($i = $data['Attemption'] - 1; $i >= 0; $i--) {


        if ($image = IconAttempt($data['Discipline_CodeScript'], $i + 1)) {
            $pdf->Image($image, $xEnd - $dX * $xAttempt + 8, 30, 7, 7, 'png');
        } else {
            $pdf->Text($xEnd - $dX * $xAttempt, 35, sprintf('%0 9s', $i + 1));
        }

        $dX++;
    }
}
$pdf->Output($data['Competition_WCA'] . '_Results_' . $data['Discipline_Code'] . ".pdf", 'I');
$pdf->Close();
exit();
