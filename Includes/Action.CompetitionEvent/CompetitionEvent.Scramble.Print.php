<?php

$requests= getRequest();
$ID=false;
if(isset($requests[2]) and is_numeric($requests[2])){
   $ID=$requests[2];
}

$Scramble_Timestamp=date("Y-m-d H:i:s");

$Letter=array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G",8=>"H",9=>"I");

$Y_Content_S=33;
$Y_Content_E=275;
$scramble_font='courier';  


$y_att=20;
$dy=33;
$dyy=3;

        
$X_IMG_0=140;

$X_IMG_1=205;

if($ID){ 
    
    $rand= random_string(20);
    mkdir("Scramble/HardTmp/$rand");
    
    DataBaseClass::FromTable('Event', "ID=$ID");
    DataBaseClass::Join_current('Scramble');
    DataBaseClass::Join('Event','DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('DisciplineFormat','Format');
    DataBaseClass::Join('Event','Competition');
    DataBaseClass::OrderClear('Scramble', 'Group');
    DataBaseClass::Order('Scramble','Attempt');
    $data=DataBaseClass::QueryGenerate();
    
    DataBaseClass::FromTable("Event","ID=$ID");
    $row=DataBaseClass::QueryGenerate(false);
    
    if(isset($row['Event_Competition'])){
        $Competition=$row['Event_Competition'];
    }else{
        $Competition=-1;
    }

    RequestClass::CheckAccessExit(__FILE__, 'Competition.Event.Settings',$Competition);

$scramble_max=0;
foreach($data as $row){
    $scramble_max=max($scramble_max,strlen($row['Scramble_Scramble']));
}

@$pdf = new FPDF('P','mm');
$pdf->SetFont('courier');
$pdf->SetLineWidth(0.3);
$group=0;    
$n=0;

if(isset($_GET['Name'])){
    $Competition_name=$_GET['Name'];
}else{
    $Competition_name=$data[0]['Competition_Name'];
}

$Competition_name=iconv('utf-8', 'cp1252//TRANSLIT', $Competition_name);

include 'Scramble/'.$data[0]['Discipline_CodeScript'].'.php';

foreach($data as $row){ 
    global  $Size;
    if($Size){
        $im=ScrambleImage($row['Scramble_Scramble'],$Size);
    }else{
        $im=ScrambleImage($row['Scramble_Scramble']);    
    }
    imagePNG($im,"Scramble/HardTmp/$rand/".$row['Scramble_ID'].".png");
    $n++;
    if($group<>$row['Scramble_Group']){
        $pdf->AddPage();    
        $group=$row['Scramble_Group'];
        $n=0;
            
      
        //Instructions
        $pdf->SetFont('Arial','',10);
        $Instructions=$row['Discipline_ScrambleComment'];
        $Instruction_rows=explode("\n",$Instructions);
        $y_instruction=10;
        foreach($Instruction_rows as $instruction_row){
            $pdf->Text(100, $y_instruction, $instruction_row);
            $y_instruction+=5;
        }
        
        //Header
        if(file_exists(ImageEventFile($row['Discipline_CodeScript']))){
            $pdf->Image(ImageEventFile($row['Discipline_CodeScript']),5,10,20,20,'jpg');
        }
        $pdf->SetFont('Arial','',24);
        $pdf->Text(10, 13, $row['Discipline_Name'].$row['Event_vRound']);
        $pdf->SetFont('Arial','',16);
        $pdf->Text(10, 20, 'Group '.$Letter[$group]);
        $pdf->Text(10, 27,$Competition_name);
        
        
        
            $pdf->SetFont('Arial','',16);
    $str=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition']);
    $pdf->Text(10, 13, $str);
    $pdf->SetFont('Arial','',24);
    $pdf->Text(10, 24, $data['Discipline'].$data['vRound']);
        
        
        
        //Footer
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->Text(10, 286,$Scramble_Timestamp);
        $Y=$Y_Content_S;
    }
        
    $pdf->Line(10,$Y,$X_IMG_1,$Y);

    $y0=43;
    
    if($n==$row['Format_Attemption']){
        $pdf->SetFont('Arial','',12);
        $pdf->SetFillColor(230,230,230);
        $pdf->Rect(17, $Y , $X_IMG_1-17, 6,'DF');
        $pdf->Text(90, $Y+4, 'Extra scrambles');
        $Y+=6;
    }
    
    
    if($n>=$row['Format_Attemption']){
        $y0=60;    
    }
    
    $pdf->SetFont('times','B',24);
    
    
    if($row['Discipline_ID']==49){
        $pdf->Text(5, $y0+$n*$dy+4, $Letter[$group]);    
    }
        
    $texts=array();
    if(strpos($row['Scramble_Scramble'],"&")===false){

        $scramble_len=strlen($row['Scramble_Scramble']);
        if($scramble_max>44*3){         $scramble_row=3; $scramble_size=12; 
        }elseif($scramble_max>38*3){    $scramble_row=3; $scramble_size=12; 
        }elseif($scramble_max>102){     $scramble_row=3; $scramble_size=16; 
        }elseif($scramble_max>90){      $scramble_row=3; $scramble_size=16; 
        }elseif($scramble_max>68){      $scramble_row=3; $scramble_size=16; 
        }elseif($scramble_max>60){      $scramble_row=2; $scramble_size=18; 
        }elseif($scramble_max>34){      $scramble_row=2; $scramble_size=18; 
        }elseif($scramble_max>20){      $scramble_row=1; $scramble_size=18; 
        }else{                          $scramble_row=1; $scramble_size=20; }

        if($scramble_len<10)$scramble_row=1;
        
        if($scramble_row==3){
            $d=8;
            $r1=ceil($scramble_len/3);
            $r2=ceil($scramble_len/3*2);
            while(substr($row['Scramble_Scramble'],$r1,1)!=" "){$r1--;}
            while(substr($row['Scramble_Scramble'],$r2,1)!=' '){$r2--;}
            $texts[]=trim(substr($row['Scramble_Scramble'],0,$r1));
            $texts[]=trim(substr($row['Scramble_Scramble'],$r1+1,$r2-$r1));
            $texts[]=trim(substr($row['Scramble_Scramble'],$r2));

        }elseif($scramble_row==2){
            $d=10;
            $r1=ceil($scramble_len/2);
            while(substr($row['Scramble_Scramble'],$r1,1)!=" "){$r1--;}
            $texts[]=trim(substr($row['Scramble_Scramble'],0,$r1));
            $texts[]=trim(substr($row['Scramble_Scramble'],$r1));
        }else{
            $texts[]=trim($row['Scramble_Scramble']);
        }

    }else{
        $texts=explode(" & ",$row['Scramble_Scramble']);
        $scramble_max=0;
        foreach($texts as $text){
            $scramble_max=max(array($scramble_max,strlen($text)));
        }
            if($scramble_max>=50){        $scramble_size=10; 
            }elseif($scramble_max>42){    $scramble_size=12; 
            }elseif($scramble_max>38){    $scramble_size=12; 
            }elseif($scramble_max>33){    $scramble_size=16; 
            }elseif($scramble_max>30){    $scramble_size=18; 
            }else{ $scramble_size=20;} 
    }
    
    $scramble_row=sizeof($texts);
    $pdf->SetFont($scramble_font,'',$scramble_size);
 
    $D_Att=($scramble_row)*$scramble_size*0.3+20;
    if($D_Att<33)$D_Att=33;

        $t=0;
        if(sizeof($texts)==1){
            $t=-10;
        }
        if(sizeof($texts)==2){
            $t=-2;
        }
        if(sizeof($texts)>3){
            $t=1;
        }
        foreach($texts as $r=>$text){
            if($r%2!=0){
                $pdf->SetFillColor(230,230,230);
                $pdf->Rect(17, $Y+$D_Att/$scramble_row*($r+1)-$scramble_size/2-2+$t , $X_IMG_0-10, $scramble_size/2,'F');
            }
            $pdf->Text(20, $Y+$D_Att/$scramble_row*($r+1) -$scramble_size*.3 +$t,$text);  

        }
        
    $pdf->SetFont('times','B',16);    
    if($row['Discipline_CutScrambles']){
        if($n+1>$row['Format_Attemption']){
            $pdf->Text(6, $Y+$D_Att/2, $Letter[$group]."E".($n+1-$row['Format_Attemption']));    
        }else{    
            $pdf->Text(8, $Y+$D_Att/2, $Letter[$group]."".($n+1));
        }
    }else{
        if($n+1>$row['Format_Attemption']){
            $pdf->Text(10, $Y+$D_Att/2, "E".($n+1-$row['Format_Attemption']));    
        }else{    
            $pdf->Text(10, $Y+$D_Att/2, $n+1);
        }    
    }
    
    $filename="Scramble/HardTmp/$rand/".$row['Scramble_ID'].".png";
    
    $size=getimagesize($filename);
    $max_width=$X_IMG_1-$X_IMG_0;
    $max_height=$D_Att-1;
    $k=min($max_width/$size[0],$max_height/$size[1]);
    $img_dx=($max_width-$k*$size[0])/2;
    $img_dy=($D_Att-$k*$size[1])/2;
    
    $pdf->Image($filename, $X_IMG_0+$img_dx, $Y+$img_dy, $k*$size[0], $k*$size[1]);
    $Y+=$D_Att;
    
    $pdf->Rect(17,$Y_Content_S,$X_IMG_1-17,$Y-$Y_Content_S);
    
}

DataBaseClass::Query("Update Event set ScrambleSalt='$rand' where ID='".$data[0]['Event_ID']."'");
$file="Image/Scramble/".$rand.".pdf";
$pdf->Output($file);
$pdf->Close();
DeleteFolder("Scramble/HardTmp/$rand");
DataBaseClass::Query("Insert into ScramblePdf (Event,Secret,Delegate,Timestamp, Action) values ('".$data[0]['Event_ID']."','$rand','". CashDelegate()['Delegate_ID']."','$Scramble_Timestamp','Generation')");
header('Location: '.PageIndex()."Scramble/".$data[0]['Event_ID']);
exit();
    
}else{
     echo 'Not found';
     exit();
}
