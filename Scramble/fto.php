<?php 
function ScrambleImage($scramble){

$Ceil=50;
$Border=20;
$D=10;
$d=0.1;
  
$Center=array(
        'U'=>array('t'=>[[1,0],[0,1]],'d'=>[1,0], 'x'=>3,'y'=>1,'Color'=>'White'),
        'BR'=>array('t'=>[[0,1],[-1,0]],'d'=>[2,1], 'x'=>5,'y'=>3,'Color'=>'Blue'),  
        'R'=>array('t'=>[[-1,0],[0,-1]],'d'=>[1,2], 'x'=>3,'y'=>5,'Color'=>'Grey'),
        'F'=>array('t'=>[[0,-1],[1,0]],'d'=>[0,1], 'x'=>1,'y'=>3,'Color'=>'Green'),
    
        'BL'=>array('t'=>[[1,0],[0,1]],'d'=>[4,0], 'x'=>9,'y'=>1,'Color'=>'Violet'),
        'L'=>array('t'=>[[0,1],[-1,0]],'d'=>[5,1], 'x'=>11,'y'=>3,'Color'=>'Red'),
        'D'=>array('t'=>[[-1,0],[0,-1]],'d'=>[4,2], 'x'=>9,'y'=>5,'Color'=>'Yellow'),
        'B'=>array('t'=>[[0,-1],[1,0]],'d'=>[3,1], 'x'=>7,'y'=>3,'Color'=>'Orange'),
  );
    
  
  $Coor=[
        '1-1'=>[['x'=>-3,'y'=>-1],['x'=>-1,'y'=>-1],['x'=>-2,'y'=> 0]],
        '1-2'=>[['x'=>-2,'y'=> 0],['x'=>-1,'y'=>-1],['x'=> 0,'y'=> 0]],
        '1-3'=>[['x'=>-1,'y'=>-1],['x'=> 1,'y'=>-1],['x'=> 0,'y'=> 0]],
        '1-4'=>[['x'=> 0,'y'=> 0],['x'=> 1,'y'=>-1],['x'=> 2,'y'=> 0]],
        '1-5'=>[['x'=> 1,'y'=>-1],['x'=> 3,'y'=>-1],['x'=> 2,'y'=> 0]],
      
        '2-1'=>[['x'=>-2,'y'=> 0],['x'=> 0,'y'=> 0],['x'=>-1,'y'=> 1]],
        '2-2'=>[['x'=>-1,'y'=> 1],['x'=> 0,'y'=> 0],['x'=> 1,'y'=> 1]],
        '2-3'=>[['x'=> 0,'y'=> 0],['x'=> 2,'y'=> 0],['x'=> 1,'y'=> 1]],
      
        '3-1'=>[['x'=>-1,'y'=> 1],['x'=> 1,'y'=> 1],['x'=> 0,'y'=> 2]],
  ];
  
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
  
$MetaCircle=[
    'U'=>['t'=>1,['BR','F','BL'],['R','L','B']],
    'D'=>['t'=>1,['B','L','R'],['BL','F','BR']],
    
    'BR'=>['t'=>1,['R','U','B'],['F','BL','D']],
    'L'=>['t'=>1,['D','BL','F'],['B','U','R']],
    
    'R'=>['t'=>1,['F','BR','D'],['U','B','L']],
    'BL'=>['t'=>1,['L','B','U'],['D','BR','F']],
    
    'F'=>['t'=>1,['U','R','L'],['BR','D','BL']],
    'B'=>['t'=>1,['BL','D','BR'],['L','R','U']],
];  
  
$circles=[];
foreach($MetaCircle as $name=>$metaCircle){
   $cir=[];
   foreach( 
        [
            ['1-1','3-1','1-5'],
            ['1-2','2-2','1-4'],
            ['2-1','2-3','1-3'],
            ['2-2','1-4','1-2'],
            ['3-1','1-5','1-1']
        ] as $l){
        $ml=$metaCircle[0];           
        if($metaCircle['t']==1){
            $cir[]=[[$ml[0],$l[0]],[$ml[1],$l[1]],[$ml[2],$l[2]]];    
        }else{
            $cir[]=[[$ml[0],$l[1]],[$ml[1],$l[0]],[$ml[2],$l[2]]];    
        }
   }
   
   foreach( 
        [
            ['3-1','1-1','1-5']
        ] as $l){
        $ml=$metaCircle[1];           
            $cir[]=[[$ml[0],$l[0]],[$ml[1],$l[1]],[$ml[2],$l[2]]];    
    }

   foreach( 
        [
            ['1-1','1-5','3-1'],
            ['1-2','1-4','2-2'],
            ['1-3','2-3','2-1']
        ] as $l){         
        $cir[]=[[$name,$l[0]],[$name,$l[1]],[$name,$l[2]]];    
   }
   $circles[$name]=$cir; 
}


$cir=[];

foreach(['U'=>'D','R'=>'BR','L'=>'BL','B'=>'F'] as $c1=>$c2){
    foreach([
        ['3-1','1-5'],
        ['2-1','2-3'],
        ['2-2','1-4'],
        ['2-3','1-3'],
        ['1-1','3-1'],
        ['1-2','2-2'],
        ['1-3','2-1'],
        ['1-4','1-2'],
        ['1-5','1-1'],
        ] as $s){
        $cir[]=[[$c1,$s[0]],[$c2,$s[1]]];
    }
}


$circles['flip']=$cir;

$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);

foreach(explode(" ",$scramble) as $move){
    $move_clear= str_replace(["\'","'"],"",$move);
    if($move_clear<>"" and isset($circles[$move_clear])){
        $direct=!(strpos($move,"'")!==false);
        $CoorColor=Rotate($CoorColor,$circles,$move_clear,$direct);
    }
} 
  

$im= imagecreate($Border*3+$Ceil*($d*4+12), $Border*2+$Ceil*($d*2+6));
$white=imagecolorallocate($im,250,255,255);
$black=imagecolorallocate($im,0,0,0);

  $Colors=array(
      'Grey'=> imagecolorallocate($im,153,153,153),
      'Red'=> imagecolorallocate($im,239,0,0),
      'Green'=> imagecolorallocate($im,0,108,0),
      'White'=> imagecolorallocate($im,254,254,254),
      'Blue'=> imagecolorallocate($im,35,0,186),
      'Yellow'=> imagecolorallocate($im,255,208,0),
      'Violet'=> imagecolorallocate($im,157,0,255),
      'Orange'=> imagecolorallocate($im,255,126,0),
      

  );
  

$Polygons=array();  
foreach($Center as $n=>$center){
  foreach ($Coor as $c=>$coor){
      $pairs=array();
      foreach($coor as $xy){
          $pairs[]=array(
              ($center['x']>6?$d:0)+
              $center['x']
                  + $xy['x']*$center['t'][0][0] 
                  + $xy['y']*$center['t'][1][0]
                  + $center['d'][0]*$d,
              $center['y']
                  + $xy['x']*$center['t'][0][1] 
                  + $xy['y']*$center['t'][1][1]
                  + $center['d'][1]*$d,
                  ) ;
      }
      $Polygons[]=array($pairs,$Colors[$CoorColor[$n][$c]]);
  }
}
foreach($Polygons as $Polygon){
    imagesetthickness($im,2);
    
    $Points=array();
    
    foreach($Polygon[0] as $point){
        $point[0]=$Border+$Ceil*$point[0];
        $point[1]=$Border+$Ceil*$point[1];
        $Points[]=$point[0];
        $Points[]=$point[1];
    }
    
    imagefilledpolygon($im,$Points,sizeof($Points)/2,$Polygon[1]);
    imagepolygon($im,$Points,sizeof($Points)/2,$black);
    
}



foreach($Center as $name=>$center){
    if($center['x']>6){
        $X=$Border+$Ceil*($center['x']+($center['d'][0]+2)*$d);
    }else{
        $X=$Border+$Ceil*($center['x']+($center['d'][0])*$d);    
    }
    $Y=$Border+$Ceil*($center['y']+$d);
    imagefilledellipse($im,$X,$Y, $Ceil, $Ceil, $white);
    imageellipse($im,$X,$Y, $Ceil, $Ceil, $black);
    $param=GetParam(20,'Fonts/Arial Bold.ttf', $name);
    imagefttext($im,  20, 0, $X-$param['weith']/2-$param['dx'], $Y+$param['height']/2-$param['dy'], $black, 'Fonts/Arial Bold.ttf', $name);
}
  return $im;
}
?>