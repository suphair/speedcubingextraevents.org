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
    DataBaseClass::OrderClear("Competitor","Name");

    
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
        $g=$command['Command_Group'];
        if($g!=-1){
            $pdf->Text(139+20*$g, 43+($n-1)*8, Group_Name($g));
        }
        $pdf->SetLineWidth(0.1);
        if($n>0){
            #$pdf->Line(10, 38+($n-1)*8 , $pdf->w - 10, 38+($n-1)*8);
        }
        #$pdf->Line(10, 38+$n*8 , $pdf->w - 5, 38+$n*8);
    
        $pdf->SetFont('Arial','B',12);
        $pdf->Text(17, 35+$n*8, $n);
    
        $pdf->SetFont('Arial','',12);
        $names=explode(",",$command['Competitor_Name']);
        foreach($names as $i=>$name){
            $names[$i]= Short_Name($name);
        }
        $pdf->Text(28, 35+$n*8, iconv('utf-8', 'cp1252//TRANSLIT',implode(", ",$names))); 
    }
    
    
        
    $pdf->SetFont('Arial','',16);
    $str=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition']);
    $pdf->Text(10, 13, $str);
    $pdf->SetFont('Arial','',24);
    $pdf->Text(10, 24, $data['Discipline'].$data['vRound']);
    
    
    #$pdf->SetLineWidth(0.3);
    #$pdf->Line(10, 38 , $pdf->w - 5, 38);
    
    $pdf->SetLineWidth(0.1);
    $pdf->SetFont('Arial','B',12);
    #$pdf->Line(15, 30, 15,32+8*$on_page);
    
    #$pdf->Line(100, 30, 100,32+8*$on_page);
    

    $pdf->SetFont('Arial','B',12);
    #$pdf->Text(45, 35, 'Name');    
    for($g=0;$g<$data['Groups'];$g++){
        $pdf->Text(130+20*$g, 35, 'Group '.Group_Name($g));
    #    $pdf->Line(100+20*($g+1), 30, 100+20*($g+1),32+8*$on_page);
    }
   
}        
$pdf->Output();              
$pdf->Close();
exit();
