<?php 
if(sizeof($commands)<=2){$rounds=1;}
elseif(sizeof($commands)<=4){$rounds=2;}
elseif(sizeof($commands)<=8){$rounds=3;}
elseif(sizeof($commands)<=16){$rounds=4;}
else{$rounds=5;}
@$pdf = new FPDF('L','mm');
    
$points=array();
$points[]=array(5,5);
$points[]=array($pdf->w /2 + 5,5);
$sizeX= $pdf->w /2 -10;
$sizeY= $pdf->h -10;    

    $pdf->AddPage();
    $pdf->SetLineWidth(0.5);
    $pdf->Line($pdf->w /2 ,5, $pdf->w /2, $pdf->h - 5);   
    for($i=0;$i<2;$i++){
        $point=$points[$i];

        $pdf->SetFont('Arial','',10);
        $pdf->SetLineWidth(0.2);

        $str=iconv('utf-8', 'cp1252//TRANSLIT', $competition);
        $pdf->Text($point[0] + 5, $point[1] + 5, $str); 
        $pdf->SetFont('Arial','U',12);
        $pdf->Text($point[0] + 5, $point[1] + 10,$data['Discipline'].$data['vRound']);

        $Ry=15;
        $pdf->SetFont('Arial','',10);
        $pdf->Rect($point[0] + 8, $point[1] + $Ry+2 ,15,10);    
        $pdf->Text($point[0] + 8, $point[1] + $Ry+1, 'ID');        
        $pdf->Rect($point[0] + 24, $point[1] + $Ry+2,55,10);    
        $pdf->Text($point[0] + 24, $point[1] + $Ry+1, 'Team');    
        $rounds=5;
        for($r=$rounds;$r>0;$r--){
            $pdf->Rect($point[0]+round($sizeX-12*($r-1)-11), $point[1] + $Ry+2 ,12,10);    
            $pdf->Text($point[0]+$sizeX-12*($r-1)-9, $point[1] + $Ry+6, $rounds-$r+1);
        }
        $pdf->Text($point[0]+$sizeX-12*(3-1)-10, $point[1] + $Ry+1, 'mark the current round');
        
        $Ry+=20;           
        $pdf->SetFont('Arial','',10);
        
        $pdf->Text($point[0] + 8, $point[1] + $Ry+1,'Scr');
        $pdf->Text($point[0] + 24, $point[1] + $Ry+1, 'Result');
        $pdf->Text($point[0] + 80, $point[1] + $Ry+1, 'Judge');
        $pdf->Text($point[0] + 96, $point[1] + $Ry+1, 'Comp');

        for($k=1;$k<=15;$k++){
            $pdf->SetFont('Arial','',24);
            if($k%3==1){
                $pdf->Text($point[0], $point[1] + $Ry+18, ceil($k/3));
                $pdf->Rect($point[0] + 112, $point[1] + $Ry+2,28,9*2);
                $pdf->SetFont('Arial','',10);
                $pdf->Text($point[0] + 112, $point[1] + $Ry+1, 'Sum of attempts');
                $pdf->Rect($point[0] + 112, $point[1] + $Ry+20,14,9);
                $pdf->Rect($point[0] + 126, $point[1] + $Ry+20,14,9);
                $pdf->Text($point[0] + 113, $point[1] + $Ry+24, 'Win');
                $pdf->Text($point[0] + 127, $point[1] + $Ry+24, 'Lose');
            }
            $pdf->Rect($point[0] + 8, $point[1] + $Ry+2,15,9);
            $pdf->Rect($point[0] + 24, $point[1] + $Ry+2,55,9);
            $pdf->Rect($point[0] + 80, $point[1] + $Ry+2,15,9);
            $pdf->Rect($point[0] + 96, $point[1] + $Ry+2,15,9);
            $Ry+=9;
            if($k%3==0){
                $Ry+=5;
            }
        }
    }
if(isset($requests[3]) and $requests[3]=='Download'){
    $pdf->Output($data['Competition_WCA'].'_ScoreCards_'.$data['Discipline_Code'].".pdf",'D');              
}else{
    $pdf->Output($data['Competition_WCA'].'_ScoreCards_'.$data['Discipline_Code'].".pdf",'I');              
}
    $pdf->Close();   
    exit();

