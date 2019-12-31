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

DataBaseClass::FromTable("Competition");
DataBaseClass::Where_current("ID=$ID");
$competition=DataBaseClass::QueryGenerate(false);
DataBaseClass::Join_current("Event");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current("Discipline");
DataBaseClass::Join("DisciplineFormat","Format");
DataBaseClass::Select("D.Name,E.ID,F.Result,D.Code,D.CodeScript");
DataBaseClass::OrderClear("Event", "Round desc");
DataBaseClass::Order("Discipline", "Name");

$disciplines=[];
foreach(DataBaseClass::QueryGenerate() as $discipline){
    if(!isset($disciplines[$discipline['Code']])){
        $disciplines[$discipline['Code']]=$discipline;
    }
}
$disciplines=array_values($disciplines);

if(!sizeof($disciplines)){
    echo 'Events not found';
    exit();
}

$pdf->AddPage();


$pdf->Image("Logo/Logo_Black.png",5,5,20,20,'png');

$pdf->SetFont('msserif','',26);
$pdf->Text(30, 18, iconv('utf-8', 'windows-1251',$competition['Competition_Name']));
$pdf->SetFont('Arial','',16);
$text =date("d.m.Y H:i",time());
$pdf->Text(30, 25,$text);
$Y_header=30;
    
$DY=min(($pdf->h-$Y_header-15)/sizeof($disciplines),30);
$X_left=5+$DY;

foreach($disciplines as $n=>$discipline){
    $sy=$DY*$n+$Y_header;
    $ey=$DY*($n+1)+$Y_header;
    $pdf->line(5,$sy,$pdf->w-5,$sy);


    DataBaseClass::FromTable("Event");
    DataBaseClass::Join_current("Command");
    DataBaseClass::Where_current("Place between 1 and 3");
    //DataBaseClass::Join_current("Competitor");
    DataBaseClass::Where("Event","ID=".$discipline['ID']);
    DataBaseClass::Join("Command","Attempt");
    DataBaseClass::Join("Command","CommandCompetitor");
    DataBaseClass::Join_current("Competitor");
    DataBaseClass::Where("Attempt","Special='".$discipline['Result']."'");
    DataBaseClass::Where("Attempt", "IsDNF=0");
    DataBaseClass::Select("Com.ID,Com.Place,A.vOut,Cm.Name");
    DataBaseClass::OrderClear("Command","Place");
    $competitors=[];
    foreach(DataBaseClass::QueryGenerate() as $row){
        if(!isset($competitors[$row['ID']])){
            $competitors[$row['ID']]=$row;
        }else{
            $competitors[$row['ID']]['Name'].=(', '.$row['Name']);
        }
    }
    
    $competitors= array_values($competitors);
    
    
    //$competitors=DataBaseClass::QueryGenerate();


    if(file_exists(ImageEventFile($discipline['CodeScript']))){
        $pdf->Image(ImageEventFile($discipline['CodeScript']),5,$sy+5,$DY-10,$DY-10,'jpg');
    }  
    if(sizeof($competitors)){
        $place_y=$DY/max(array(sizeof($competitors),3))*0.9;    

//            $pdf->SetFont('msserif','',min(array($place_y*2,10)));
//            $text = iconv('utf-8', 'windows-1251',$discipline['Name']);
//            $pdf->Text($X_left, $sy+$place_y*1,$text);   

        foreach($competitors as $n=>$competitor){
            $pdf->SetFont('Arial','B',min(array($place_y*2,14)));
            $pdf->Text($X_left, $sy+$place_y*($n+1),$competitor['Place']);   
            $pdf->SetFont('Arial','',min(array($place_y*2,14)));
            $pdf->Text($X_left+5, $sy+$place_y*($n+1),sprintf("%10s",$competitor['vOut']));               
            $pdf->Text($X_left+35, $sy+$place_y*($n+1),iconv('utf-8', 'cp1252//TRANSLIT',$competitor['Name']));   
        }
    }
}
$sy=$DY*sizeof($disciplines)+$Y_header;
$ey=$DY*sizeof($disciplines)+$Y_header;
$pdf->line(5,$sy,$pdf->w-5,$sy);
$pdf->SetFont('Arial','',18);
$pdf->Text(60, 290,GetIni('TEXT','print'));
$pdf->Output($competition['Competition_WCA'].'_'.'Pedestal'.".pdf",'I');              
$pdf->Close();
exit();
