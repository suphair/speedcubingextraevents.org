<?php
if($_FILES['file']['error']==0 and $_FILES['file']['type'] == 'application/pdf'){ 
    
    CheckPostIsset('ID');
    CheckPostIsNumeric('ID');
    CheckPostNotEmpty('ID');
    $ID=$_POST['ID'];

    $Scramble_Timestamp=date("Y-m-d H:i:s");
    
    if(isset($row['Event_Competition'])){
        $Competition=$row['Event_Competition'];
    }else{
        $Competition=-1;
    }
    
    RequestClass::CheckAccessExit(__FILE__,'Competition.Settings',$Competition);
    
    $pdf_file = $_FILES['file']['tmp_name'];
    
    $im = new imagick();
    $im->readimage($_FILES['file']['tmp_name']); 
    $Pages=$im->getnumberimages();
    $rand= random_string(20);
    
    $lines=[];
    
    mkdir("Scramble/HardTmp/{$rand}");
    for($i=0;$i<$Pages;$i++){
        $im = new imagick();
        $im->setResolution(300,300);
        $im->readimage($pdf_file."[$i]"); 
        $im->setImageFormat('jpeg');    
        $jpg_file =  "Scramble/HardTmp/{$rand}/{$i}.jpg" ;
        $im->writeImage($jpg_file); 
        $im->clear(); 
        $im->destroy();
        
        $img_lines= imagecreatefromjpeg($jpg_file);
        
        $B=0;
        for($y=250;$y<3050;$y++){
            if(in_array(imagecolorat($img_lines, 250, $y),[0,65793]) 
               and in_array(imagecolorat($img_lines, 250, $y+1),[0,65793]) 
               and in_array(imagecolorat($img_lines, 310, $y),[0,65793]) 
               and in_array(imagecolorat($img_lines, 310, $y+1),[0,65793]) 
              ){
                if(isset($lines[$i][$B][0]) and $y-$lines[$i][$B][0]<100){
                    $lines[$i][$B][0]=$y;
                }else{                
                    $lines[$i][$B+1][0]=$y;
                    if($B>0){
                        $lines[$i][$B][1]=$y+2;
                    }
                    $B++;
                }
                $y+=10;
            }
        }
        unset($lines[$i][$B]);
    }
   
    
    Databaseclass::FromTable('Event', "ID='$ID'");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('DisciplineFormat','Format');
    Databaseclass::Join('Event','Competition');
    $data=Databaseclass::QueryGenerate(false);
    $Discipline=$data['Discipline_CodeScript'];
    $X0=285;
    $X1=2353;
    
    $X=$X1-$X0;
    

    @$pdf = new FPDF('P','mm');
    
    $pdf_img_Y0=35;
    $pdf_img_X0=20;
    $pdf_img_X=$pdf->w-$pdf_img_X0;
    $pdf_img_Y=$pdf_img_Y0;
    
    $Letter=array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G",8=>"H",9=>"I");
    
    $pdf->SetFont('courier');   
    $Groups=$data['Event_Groups'];
    $Attemption=$data['Format_Attemption'];
    if($Discipline=='all_scr'){
        $Attemption=1;    
    }
    
    
    $ScamblesOnePage=(5+2);
    $ScramblesEvent=$Groups*($Attemption+1);
    $PagesEvent=ceil($ScramblesEvent/$ScamblesOnePage);

    if(strpos($data['Discipline_CodeScript'],'mguild')!==false){
        $data['Discipline_TNoodles']='555,444,333,222,minx,pyram,333oh,sq1,skewb,clock';
    }
    
    if($data['Discipline_TNoodlesMult']){
        $arr=[];
        for($i=0;$i<$data['Discipline_TNoodlesMult'];$i++){
            $arr[]=$data['Discipline_TNoodles'];
        }
        $data['Discipline_TNoodles']=implode(",",$arr);
    }
    
    $attemption_event=explode(',',$data['Discipline_TNoodles']);
    
    for($group=1;$group<=$Groups;$group++){
        for($attemption=1;$attemption<=($Attemption+1);$attemption++){
            $StartPage=floor((($group-1)*($Attemption+1)+$attemption)/$ScamblesOnePage);
            $PageAdd=0;
            $AttemptScrambling=0;
            $PageNumberPDF=0;
            for($BasePage=$StartPage;$BasePage<$Pages;$BasePage+=$PagesEvent){
                $AttemptScrambling++;
                $CurrentPage=$PageAdd+$BasePage;
                if(!isset($ScambleOnPage[$CurrentPage])){
                    $ScambleOnPage[$CurrentPage]=0;
                }
                $ScambleOnPage[$CurrentPage]++;
                $StartLine=$lines[$CurrentPage][$ScambleOnPage[$CurrentPage]][0];
                $EndLine=$lines[$CurrentPage][$ScambleOnPage[$CurrentPage]][1];
                
                if(($pdf_img_Y+$pdf_img_X/$X*($EndLine-$StartLine+1))>($pdf->h-10)){
                    $NextPagePDF=true;
                }else{
                    $NextPagePDF=false;    
                }
                
                if($BasePage==$StartPage or $NextPagePDF){
                    $PageNumberPDF++;
                    $pdf->AddPage();

                    $pdf->Image(ImageEventFile($Discipline),5,10,20,20,'jpg');
                    $pdf->Image("Logo/Logo_Color.png",$pdf->w-25,10,20,20,'png');

                    $pdf->SetFont('Arial','',16);
                    $pdf->SetTextColor(17,31,135);
                    
                    $Competition_name=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition_Name']);
                    $pdf->Text(30, 13, $Competition_name); 
        
                    $pdf->SetFont('msserif','',16);
                    $pdf->SetTextColor(162,0,0);
                    $pdf->Text(30, 20, $data['Discipline_Name'].$data['Event_vRound']);
                    $pdf->SetFont('Arial','',16);
                    
                    $pdf->SetTextColor(0,182,67);
                    if($attemption<$Attemption+1){                    
                        $pdf->Text(30, 27, $Letter[$group].' group                    '. $attemption. ' attempt                    '.$PageNumberPDF.' page');
                    }else{
                        $pdf->Text(30, 27, $Letter[$group].' group                    Extra attempt                    '.$PageNumberPDF.' page');
                    }
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->Text(80, 286,GetIni('TEXT','print'));
                    $pdf->SetFont('Arial','',10);
                    $pdf->Text(150, 286,$Scramble_Timestamp);
                    
                    $pdf_img_Y=$pdf_img_Y0;
                }

                $image_cut=imagecreatetruecolor($X, $EndLine-$StartLine+1);
                imagecolorallocate($image_cut, 0,0, 0);
                
                
                
                imagecopy($image_cut, imagecreatefromjpeg("Scramble/HardTmp/{$rand}/{$CurrentPage}.jpg"), 0, 0, $X0, 
                    $StartLine, $X, $EndLine-$StartLine+1);
                
                
                $file_tmp="Scramble/HardTmp/{$rand}/{$CurrentPage}_{$group}_{$attemption}.png";
                imagepng($image_cut, $file_tmp);

                $pdf->SetFont('times','B',24);

                if($data['Discipline_CutScrambles']){
                    if($AttemptScrambling>=10){
                        $pdf->SetFont('times','B',16);
                        $pdf->Text(10, $pdf_img_Y+10, $Letter[$group].$AttemptScrambling);
                    }else{
                        $pdf->Text(10, $pdf_img_Y+10, $Letter[$group].$AttemptScrambling);
                    }
                    if($attemption==$Attemption+1){
                        $pdf->SetFont('times','B',20);
                        $pdf->Text(10, $pdf_img_Y+28, 'EX');
                    }
                    
                }else{
                    $pdf->Text(10, $pdf_img_Y+10, $AttemptScrambling);
                }           
                if(isset($attemption_event[$AttemptScrambling-1])){
                    $pdf->Image("Image/Events/".$attemption_event[$AttemptScrambling-1].".png",10,$pdf_img_Y+12,10,10);
                }else{
                    $pdf->Text(10, $pdf_img_Y+12, $AttemptScrambling-1);
                }
                
                $pdf->Image($file_tmp,
                        $pdf_img_X0,
                        $pdf_img_Y,
                        $pdf_img_X-$pdf_img_X0,
                        $pdf_img_X/$X*($EndLine-$StartLine+1));
                $pdf_img_Y+=$pdf_img_X/$X*($EndLine-$StartLine+1)+1;
                
                
                if($ScambleOnPage[$CurrentPage]==$ScamblesOnePage){
                    $PageAdd++;
                }
            }
            if(strpos($data['Discipline_CodeScript'],'mguild')!==false){
                $pdf->SetFont('times','B',18);
                $mguild_X0=10;
                $mguild_Y0=$pdf_img_Y+20;
                $pdf->Text(85,$mguild_Y0-2, 'Competitor');
                
                $mguild_X1=$pdf->w-10;
                $mguild_Y1=$pdf->h-20;        
                $pdf->Rect($mguild_X0,$mguild_Y0-10,$mguild_X1-$mguild_X0,$mguild_Y1-$mguild_Y0+10);
                
                $mguild_events=[
                    ['555'=>'5x5x5 Cube','444'=>'4x4x4 Cube','333'=>'3x3x3 Cube','222'=>'2x2x2 Cube'],
                    ['minx'=>'Megaminx','pyram'=>'Pyraminx','333oh'=>'3x3x3 One-Handed'],
                    ['sq1'=>'Square-1','skewb'=>'Skewb','clock'=>'Clock']
                ];
                
                $mguild_line=[];
                $mguild_ceil=[];
                for($mguild_i=0;$mguild_i<sizeof($mguild_events);$mguild_i++){
                    $mguild_line[$mguild_i]=$mguild_Y0+($mguild_Y1-$mguild_Y0)/sizeof($mguild_events)*$mguild_i;
                    $mguild_j=0;
                    foreach($mguild_events[$mguild_i] as $code=>$name){
                        $mguild_ceil[$mguild_i][$code]=$mguild_X0+($mguild_X1-$mguild_X0)/sizeof($mguild_events[$mguild_i])*$mguild_j;
                        $mguild_j++;
                    }
                }
                $mguild_line[sizeof($mguild_events)]=$mguild_Y1;
                
                $pdf->SetFont('times','B',18);
                for($mguild_i=0;$mguild_i<sizeof($mguild_events);$mguild_i++){
                    $pdf->Line($mguild_X0, $mguild_line[$mguild_i], $mguild_X1, $mguild_line[$mguild_i]);   
                    foreach($mguild_events[$mguild_i] as $code=>$name){
                        $pdf->Line($mguild_ceil[$mguild_i][$code], $mguild_line[$mguild_i], $mguild_ceil[$mguild_i][$code], $mguild_line[$mguild_i+1]);   
                        $pdf->Image("Image/Events/$code.png",$mguild_ceil[$mguild_i][$code]+5, $mguild_line[$mguild_i]+5, 15, 15);
                        $pdf->Text($mguild_ceil[$mguild_i][$code]+5,$mguild_line[$mguild_i]+30, $name);
                    }
                }
            }
        }
    }   
    DataBaseClass::Query("Update Event set ScrambleSalt='$rand' where ID=".$data['Event_ID']);
    $file="Image/Scramble/".$rand.".pdf";
    $pdf->Output($file);
    $pdf->Close();
    DeleteFolder("Scramble/HardTmp/$rand");
    DataBaseClass::Query("Insert into ScramblePdf (Event,Secret,Delegate,Timestamp,Action) values ('".$data['Event_ID']."','$rand','". CashDelegate()['Delegate_ID']."','$Scramble_Timestamp','Generation')");
    
    
    header('Location: '.PageIndex()."Scramble/".$data['Event_ID']);
    exit();
}