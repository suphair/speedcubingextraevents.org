<?php
$requests=getRequest();
if(!isset($requests[2]) or !is_numeric($requests[2])){
    echo 'Wrong competition ID';
    exit();
}else{
   $ID=$requests[2];
}

RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings',$ID);

        
@$pdf = new FPDF('P','mm');
$pdf->SetFont('courier');

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

$max_page=30;    
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
            $pdf->Rect(5, $dY+3+($n-1)*8, $pdf->w - 10, 8, "F");
        }
        
        $pdf->SetLineWidth(0.1);
        if($n>0){
            $pdf->Line(5, $dY+3+($n-1)*8 , $pdf->w - 5, $dY+3+($n-1)*8);
        }
        $pdf->Line(5, $dY+3+$n*8 , $pdf->w - 5, $dY+3+$n*8);
    
        $pdf->SetFont('Arial','',12);
        $pdf->Text(7, $dY+$n*8, iconv('utf-8', 'cp1252//TRANSLIT',$competitor['Competitor']));    
        
        foreach($events as $e=>$event){
            if(isset($competitor[$event])){
                if($competitor[$event]==-1){
                    $pdf->Text(100+$e*10+2, $dY+$n*8, '*');      
                }else{
                    $pdf->Text(100+$e*10+2, $dY+$n*8, Group_Name($competitor[$event]));  
                }
            }
        }
    
    }
    
    $pdf->Image("Logo/Logo_Black.png",5,5,20,20,'png');
    
    $pdf->SetFont('msserif','',26);
    $pdf->Text(30, 18, iconv('utf-8', 'windows-1251',$Competition_name));
    
    $pdf->Text(190, 13, ($p+1)."/$pages");

    
    $pdf->SetLineWidth(0.1);
    $pdf->SetFont('Arial','B',10);
//    $pdf->Line(15, 30, 15,32+8*$on_page);
    
//    $pdf->Line(95, 30, 95,32+8*$on_page);
    
//    $pdf->Line(135, 30, 135,32+8*$on_page);
    $pdf->SetFont('Arial','B',14);
    $pdf->Text(8, $dY, 'Competitor');  
    
    
    foreach($events as $e=>$event){
        $pdf->Image(ImageEventFile($event),100+$e*10,$dY-5,7,7,'jpg');
    }
    
}        
$pdf->Output();              
$pdf->Close();
exit();