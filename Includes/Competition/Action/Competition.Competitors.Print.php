<?php
$requests=getRequest();
if(!isset($requests[2]) or !is_numeric($requests[2])){
    echo 'Wrong competition ID';
    exit();
}else{
   $ID=$requests[2];
}

RequestClass::CheckAccessExit(__FILE__, 'Competition.Event.Settings',$ID);

        
@$pdf = new FPDF('L','mm');
$max_page=20;  
$pdf->SetTextColor(33,33,33);

DataBaseClass::FromTable('Competition',"ID=$ID");
DataBaseClass::Join_current("Event");
DataBaseClass::Join_current("Command");
DataBaseClass::Join_current("CommandCompetitor");
DataBaseClass::Join_current("Competitor");
DataBaseClass::Join("Event","DisciplineFormat");
DataBaseClass::Join_current("Discipline");
DataBaseClass::OrderClear("Competitor","Name");
DataBaseClass::Order("Discipline","Code");

$competitors=DataBaseClass::QueryGenerate();
if(!sizeof($competitors)){
    echo 'Competitors not found';
    exit();
}
$Competition_name=$competitors[0]['Competition_Name'];
$data=[];
$events=[];
foreach($competitors as $competitor){
    $data[$competitor['Competitor_ID']]['Competitor']=Short_Name($competitor['Competitor_Name']).' '.$competitor['Competitor_WCAID'];
    $data[$competitor['Competitor_ID']][$competitor['Discipline_CodeScript']]=$competitor['Command_Group'];
    $events[$competitor['Discipline_CodeScript']]=$competitor['Discipline_CodeScript'];
}

$data=array_values($data);
$events=array_values($events);

  
$pages=ceil(sizeof($data)/$max_page);

$dY=34;
for($p=0;$p<$pages;$p++){  
    $start = $p * $max_page;
    $end = min (array(($p+1) * $max_page,sizeof($data)));
    $on_page=($end-$start+1);
    $pdf->AddPage();    

    $n=0;
    for($c=$start;$c<$end;$c++){
        $competitor=$data[$c];
        $n++;

        if($c%2 ==0){
            $pdf->SetFillColor(240,240,240);
            $pdf->Rect(10, $dY+3+($n-1)*8, $pdf->w - 20, 8, "F");
        }
        
        $pdf->SetLineWidth(0.1);
        if($n>0){
            //$pdf->Line(10, $dY+3+($n-1)*8 , $pdf->w - 10, $dY+3+($n-1)*8);
        }
        //$pdf->Line(10, $dY+3+$n*8 , $pdf->w - 10, $dY+3+$n*8);
    
        $pdf->SetFont('Arial','',12);
        $pdf->Text(12, $dY+$n*8, iconv('utf-8', 'cp1252//TRANSLIT',$competitor['Competitor']));    
        
        foreach($events as $e=>$event){
            if(isset($competitor[$event])){
                if($competitor[$event]==-1){
                    $pdf->Text(100+$e*25+2, $dY+$n*8, '*');      
                }else{
                    $pdf->Text(100+$e*25+2, $dY+$n*8, Group_Name($competitor[$event]));  
                }
            }
        }
    
    }
    
    
    $pdf->SetFont('Arial','',16);
    $pdf->Text(10, 13,($p+1)."/$pages");
    $pdf->SetFont('Arial','',24);
    $str=iconv('utf-8', 'cp1252//TRANSLIT', $Competition_name);
    $pdf->Text(10, 24,$str);
    
    
    #$pdf->SetFont('Arial','',26);
    #$pdf->Text(5, 18, iconv('utf-8','cp1252//TRANSLIT',$Competition_name));
    
    #$pdf->Text(190, 13, ($p+1)."/$pages");

    
    $pdf->SetLineWidth(0.1);
 //   $pdf->SetFont('Arial','B',10);
//    $pdf->Line(15, 30, 15,32+8*$on_page);
    
//    $pdf->Line(95, 30, 95,32+8*$on_page);
    
//    $pdf->Line(135, 30, 135,32+8*$on_page);
    $pdf->SetFont('Arial','B',12);
//    $pdf->Text(8, $dY, 'Competitor');  
    
    
    foreach($events as $e=>$event){
        $pdf->Text(100+$e*25,$dY, $event);  
        
        #$pdf->Image(ImageEventFile($event),100+$e*10,$dY-5,7,7,'jpg');
    }
    
}        
$pdf->Output();              
$pdf->Close();
exit();