<?php
$requests= getRequest();
if(!isset($requests[2]) or !is_numeric($requests[2])){
    echo 'Wrong event ID';
    exit();
}else{
   $ID=$requests[2];
}

    DataBaseClass::FromTable("Event","ID=$ID");
    $row=DataBaseClass::QueryGenerate(false);

    if(isset($row['Event_Competition'])){
        $Competition=$row['Event_Competition'];
        RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$Competition);;
    }else{
        echo 'Wrong event ID';
        exit();
    }
    $max_page=20;    
    @$pdf = new FPDF('L','mm');
    $pdf->SetTextColor(33,33,33);

    DataBaseClass::FromTable('Command');
    DataBaseClass::Join_current('Event');
    DataBaseClass::Where_current("ID=$ID");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('Event','Competition');
    DataBaseClass::Join("Command","CommandCompetitor");
    DataBaseClass::Join_current("Competitor");
    DataBaseClass::OrderClear("Command","CardID");

    
    $commands=[];
    foreach(DataBaseClass::QueryGenerate() as $row){
        if(!isset($commands[$row['Command_ID']])){
            $commands[$row['Command_ID']]=$row;
        }else{
            $commands[$row['Command_ID']]['Competitor_Name'].=(', '.$row['Competitor_Name']);
        }
    }
    $commands= array_values($commands); 
      
    DataBaseClass::Query("select  E.vRound, C.Name Competition, D.Name Discipline,D.CodeScript, C.WCA Competition_WCA, D.Code Discipline_Code, E.Groups from `Discipline` D "
    . " join `DisciplineFormat` DF on DF.Discipline = D.ID "
    . " join `Event` E on E.DisciplineFormat = DF.ID "
    ." join Competition C on C.ID=E.Competition where E.ID='$ID'");
    $data=DataBaseClass::getRow();
        
       
    $pages=ceil(sizeof($commands)/$max_page);
    
for($p=0;$p<$pages;$p++){  
    $start = $p * $max_page;
    $end = min (array(($p+1) * $max_page,sizeof($commands)));
    $on_page=($end-$start+1);
    $pdf->AddPage();    

    $n=0;
    for($c=$start;$c<$end;$c++){
        $command=$commands[$c];
        $n++;

        if($c%2 ==0){
            $pdf->SetFillColor(240,240,240);
            $pdf->Rect(10, 38+($n-1)*8, $pdf->w - 20, 8, "F");
        }
        $pdf->SetFont('Arial','',12);
        $pdf->Text(17, 35+$n*8, $command['Command_CardID']);
        $pdf->SetFont('Arial','B',12);
        $pdf->Text(27, 35+$n*8, $command['Command_Name']);
    
        $pdf->SetFont('Arial','',12);
        $names=explode(",",$command['Competitor_Name']);
        foreach($names as $i=>$name){
            $names[$i]= Short_Name($name);
        }
        $pdf->Text(90, 35+$n*8, iconv('utf-8', 'cp1252//TRANSLIT',implode(", ",$names))); 
    }
    
    
        
    $pdf->SetFont('Arial','',16);
    $str=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition']);
    $pdf->Text(10, 13, $str);
    $pdf->SetFont('Arial','',24);
    $pdf->Text(10, 24, $data['Discipline'].$data['vRound']);
    
    $pdf->SetLineWidth(0.1);
    $pdf->SetFont('Arial','B',12);
    $pdf->Text(27, 35, 'Team');
    $pdf->Text(17, 35, 'ID');
    $pdf->Text(90, 35, 'Competitors');
}        
$pdf->Output();              
$pdf->Close();
exit();
