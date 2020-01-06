<?php 
function ScrambleImage($scramble){

$im = imageCreateFromPng("Scramble/Template/Kilominx.png");
  
  $Colors=array(
      'Red'=> imagecolorallocate($im,255,0,0),
      'Green'=> imagecolorallocate($im,49,127,67),
      'White'=> imagecolorallocate($im,255,255,255),
      'Blue'=> imagecolorallocate($im,0,0,255),
      'Yellow'=> imagecolorallocate($im,255,255,0),
      'Violet'=> imagecolorallocate($im,139,0,255),
      
      'Grey'=> imagecolorallocate($im,128,128,128),
      'Pink'=> imagecolorallocate($im,255,192,203),
      'LightGreen'=> imagecolorallocate($im,144,238,144),
      'Orange'=> imagecolorallocate($im,255,165,0),
      'LightBlue'=> imagecolorallocate($im,66,170,255),
      'LightYellow'=> imagecolorallocate($im,230,215,176),
    
  );
  
  $Center=array(
        1=>array('x'=>406,'y'=>654,'P'=>'D','Color'=>'Green'),
        2=>array('x'=>174,'y'=>484,'P'=>'D','Color'=>'Violet'),
        3=>array('x'=>263,'y'=>212,'P'=>'D','Color'=>'Yellow'),
        4=>array('x'=>548,'y'=>213,'P'=>'D','Color'=>'Blue'),
        5=>array('x'=>641,'y'=>481,'P'=>'D','Color'=>'Red'),
        6=>array('x'=>406,'y'=>413,'P'=>'U','Color'=>'White'),
      
        7=>array('x'=>872,'y'=>407,'P'=>'U','Color'=>'Pink'),
        8=>array('x'=>1101,'y'=>250,'P'=>'U','Color'=>'LightGreen'),
        9=>array('x'=>1335,'y'=>412,'P'=>'U','Color'=>'Orange'),
        'A'=>array('x'=>1245,'y'=>680,'P'=>'U','Color'=>'LightBlue'),
        'B'=>array('x'=>960,'y'=>681,'P'=>'U','Color'=>'LightYellow'),
        'C'=>array('x'=>1100,'y'=>489,'P'=>'D','Color'=>'Grey'),
  );
  
  
  $Coor=array(
      'D'=>array(
            1=>array('x'=>0,'y'=>50),
            2=>array('x'=>-50,'y'=>0),
            3=>array('x'=>-50,'y'=>-50),
            4=>array('x'=>50,'y'=>-50),
            5=>array('x'=>50,'y'=>0)
        ),
        'U'=>array(
            1=>array('x'=>0,'y'=>-50),
            2=>array('x'=>50,'y'=>0),
            3=>array('x'=>50,'y'=>50),
            4=>array('x'=>-50,'y'=>50),
            5=>array('x'=>-50,'y'=>0)
        )
  );
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor[$center['P']] as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
  $circles=array(
          'U'=>[['61','62','63','64','65'],['42','53','14','25','31'],['35','41','52','13','24']],
          'R'=>[['51','52','53','54','55'],['63','41','75','B5','15'],['62','45','74','B4','14']],
          'F'=>[['11','12','13','14','15'],['63','51','B3','A3','25'],['64','52','B4','A4','21']],
          'L'=>[['21','22','23','24','25'],['65','13','A3','93','32'],['64','12','A2','92','31']],
          'BR'=>[['41','42','43','44','45'],['61','34','85','75','53'],['62','35','81','71','54']],
          'BL'=>[['31','32','33','34','35'],['61','24','92','82','43'],['65','23','91','81','42']],
      );
              
  foreach(['1'=>'8','2'=>'9','3'=>'A','4'=>'B','5'=>'7','6'=>'C'] as $s1=>$s2){
      for($i=1;$i<=5;$i++){
         $circles['flip'][]=[$s1.$i,$s2.$i]; 
      }
  }
  
  
foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    $direct=strpos($move,"'")===false;
    $double=strpos($move,"2")!==false;
    $move= str_replace(["'",'2'], "", $move);
    if($move<>""){
        if(isset($circles[$move])){
            if($double){
                $CoorColor=Rotate($CoorColor,$circles,$move,$direct);                  
            }
            $CoorColor=Rotate($CoorColor,$circles,$move,$direct);              
        }else{
            echo $move;
            exit();
        }
    }
}
   
  foreach($Center as $n=>$center){
    foreach ($Coor[$center['P']] as $c=>$coor){
        imagefill($im,($center['x']+$coor['x']), ($center['y']+$coor['y']), $Colors[$CoorColor[$n][$c]]);       
        //imageellipse($im, $center['x']+$coor['x'], $center['y']+$coor['y'], 100, 100,$Colors['Red']);
    }
  }
  return $im;
}
?>