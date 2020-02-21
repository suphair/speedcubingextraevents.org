<?php 
function ScrambleImage($scramble,$training=false){
#$scramble='UR UL UF UB UL UB UL UF UL UF UL UB UR UF UR';
$Ceil=50;
$Border=10;
$D=20;
  
$Center=array(
        'F'=>array('x'=>3,'y'=>3,'Color'=>'Green'),
        'D'=>array('x'=>3,'y'=>6,'Color'=>'Yellow'),
        'L'=>array('x'=>0,'y'=>3,'Color'=>'Orange'),
      
        'U'=>array('x'=>3,'y'=>0,'Color'=>'White'),
        'R'=>array('x'=>6,'y'=>3,'Color'=>'Red'),
        'B'=>array('x'=>9,'y'=>3,'Color'=>'Blue'),
  
  );
  
  
$Coors=array(
    
            'ul'=>array(array('x'=>0,'y'=>0),array('x'=>1,'y'=>0),array('x'=>0,'y'=>1)),
            'ur'=>array(array('x'=>2,'y'=>0),array('x'=>3,'y'=>0),array('x'=>3,'y'=>1)),
            'dr'=>array(array('x'=>2,'y'=>3),array('x'=>3,'y'=>3),array('x'=>3,'y'=>2)),
            'dl'=>array(array('x'=>0,'y'=>2),array('x'=>0,'y'=>3),array('x'=>1,'y'=>3)),
    
            'u'=>array(array('x'=>1,'y'=>0),array('x'=>1.5,'y'=>1.5),array('x'=>2,'y'=>0)),
            'r'=>array(array('x'=>3,'y'=>1),array('x'=>1.5,'y'=>1.5),array('x'=>3,'y'=>2)),
            'd'=>array(array('x'=>1,'y'=>3),array('x'=>1.5,'y'=>1.5),array('x'=>2,'y'=>3)),
            'l'=>array(array('x'=>0,'y'=>1),array('x'=>1.5,'y'=>1.5),array('x'=>0,'y'=>2)),
    
            'UL'=>[['x'=>0,'y'=>1],['x'=>1,'y'=>0],['x'=>1.5,'y'=>1.5]],
            'UR'=>[['x'=>3,'y'=>1],['x'=>2,'y'=>0],['x'=>1.5,'y'=>1.5]],
            'DL'=>[['x'=>0,'y'=>2],['x'=>1,'y'=>3],['x'=>1.5,'y'=>1.5]],
            'DR'=>[['x'=>3,'y'=>2],['x'=>2,'y'=>3],['x'=>1.5,'y'=>1.5]],
);        

  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coors as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
    $circles=array(
        'UF'=>[[['U','d'],['F','u']],[['U','DR'],['F','UL']],[['U','DL'],['F','UR']],[['U','dr'],['F','ul']],[['U','dl'],['F','ur']],[['R','ul'],['L','ur']]],
        'UB'=>[[['B','u'],['U','u']],[['B','UR'],['U','UL']],[['B','UL'],['U','UR']],[['B','ur'],['U','ul']],[['B','ul'],['U','ur']],[['R','ur'],['L','ul']]],
        'DF'=>[[['F','d'],['D','u']],[['F','DR'],['D','UL']],[['F','DL'],['D','UR']],[['F','dr'],['D','ul']],[['F','dl'],['D','ur']],[['R','dl'],['L','dr']]],
        'DB'=>[[['D','d'],['B','d']],[['D','DR'],['B','DL']],[['D','DL'],['B','DR']],[['D','dr'],['B','dl']],[['D','dl'],['B','dr']],[['R','dr'],['L','dl']]],
        'RF'=>[[['F','r'],['R','l']],[['F','ur'],['R','dl']],[['F','dr'],['R','ul']],[['F','UR'],['R','DL']],[['F','DR'],['R','UL']],[['U','dr'],['D','ur']]],
        'LF'=>[[['L','r'],['F','l']],[['L','ur'],['F','dl']],[['L','dr'],['F','ul']],[['L','UR'],['F','DL']],[['L','DR'],['F','UL']],[['U','dl'],['D','ul']]],
        'RB'=>[[['R','r'],['B','l']],[['R','ur'],['B','dl']],[['R','dr'],['B','ul']],[['R','UR'],['B','DL']],[['R','DR'],['B','UL']],[['U','ur'],['D','dr']]],
        'LB'=>[[['B','r'],['L','l']],[['B','ur'],['L','dl']],[['B','dr'],['L','ul']],[['B','UR'],['L','DL']],[['B','DR'],['L','UL']],[['U','ul'],['D','dl']]],
        'UR'=>[[['U','r'],['R','u']],[['U','ur'],['R','ul']],[['U','dr'],['R','ur']],[['U','UR'],['R','UL']],[['U','DR'],['R','UR']],[['F','ur'],['B','ul']]],
        'UL'=>[[['U','l'],['L','u']],[['U','ul'],['L','ur']],[['U','dl'],['L','ul']],[['U','UL'],['L','UR']],[['U','DL'],['L','UL']],[['F','ul'],['B','ur']]],
        'DR'=>[[['D','r'],['R','d']],[['D','ur'],['R','dr']],[['D','dr'],['R','dl']],[['D','UR'],['R','DR']],[['D','DR'],['R','DL']],[['F','dr'],['B','dl']]],
        'DL'=>[[['D','l'],['L','d']],[['D','ul'],['L','dl']],[['D','dl'],['L','dr']],[['D','UL'],['L','DL']],[['D','DL'],['L','DR']],[['F','dl'],['B','dr']]],
    );
              
  
$break=false;    
foreach(explode(" ",$scramble) as $m=>$move){
    $move=trim($move);
    if(isset($circles[$move])){
        $CoorColor=Rotate($CoorColor,$circles,$move,true);                  
    }else{
        if($move=='[verify]'){
            break;
        }else{
            echo 'ERROR '.$move;
            exit();
        }
    }
}
  

$im= imagecreate($Border*2+$Ceil*3*4+$D*3, $Border*2+$Ceil*3*3+$D*2);
$white=imagecolorallocate($im,255,255,255);
$black=imagecolorallocate($im,0,0,0);

  $Colors=array(
      'Red'=> imagecolorallocate($im,255,0,0),
      'Green'=> imagecolorallocate($im,49,127,67),
      'White'=> imagecolorallocate($im,255,255,255),
      'Blue'=> imagecolorallocate($im,0,0,255),
      'Yellow'=> imagecolorallocate($im,255,255,0),
      'Orange'=> imagecolorallocate($im,255,165,0),
      
      'Black'=> imagecolorallocate($im,0,0,0),
  );
  
  
   
$Polygons=array();  
foreach($Center as $n=>$center){
  foreach ($Coors as $c=>$coor){
      $pairs=array();
      foreach($coor as $xy){$pairs[]=array($center['x']+$xy['x'],$center['y']+$xy['y']) ;}
      $Polygons[]=array($pairs,$Colors[$CoorColor[$n][$c]]);
  }
}

foreach($Polygons as $Polygon){
    imagesetthickness($im,2);
    
    $minX=10000;
    $minY=10000;
    foreach($Polygon[0] as $point){
        if($minX>$point[0])$minX=$point[0];
        if($minY>$point[1])$minY=$point[1];
    }

    $dx=floor($minX/3)*$D;
    $dy=floor($minY/3)*$D;
    $Points=array();
    foreach($Polygon[0] as $point){
        $point[0]=$dx+$Border+$Ceil*$point[0];
        $point[1]=$dy+$Border+$Ceil*$point[1];
        $Points[]=$point[0];
        $Points[]=$point[1];
    }
    
    imagefilledpolygon($im,$Points,sizeof($Points)/2,$Polygon[1]);
    imagepolygon($im,$Points,sizeof($Points)/2,$black);
}

    if($training){

        $moveNames=[
            'UF'=>[4.5,3,1,.5],
            'UR'=>[6,1.5,1,0],
            'UL'=>[3,1.5,1,0],
            'UB'=>[4.5,0,1,0],
            'LB'=>[0,4.5,0,1],
            'LF'=>[3,4.5,0.5,1],
            'RB'=>[9,4.5,2.5,1],
            'RF'=>[6,4.5,1.5,1],
            'DB'=>[4.5,9,1,2],
            'DR'=>[6,7.5,1,2],
            'DL'=>[3,7.5,1,2],
            'DF'=>[4.5,6,1,1.5],
        ];

        imagesetthickness($im,2);
        foreach($moveNames as $name=>$coor){
            $X=$D*$coor[2]+$Border+$coor[0]*$Ceil;
            $Y=$D*$coor[3]+$Border+$coor[1]*$Ceil;
            imagefilledellipse($im,$X, $Y, 45, 45, $Colors['White']);
            imageellipse($im,$X, $Y, 45, 45, $Colors['Black']);
            $param=GetParam(16,'Fonts/arial.ttf', $name);
            imagefttext($im,16, 0, $X-$param['weith']/2-$param['dx'], $Y+$param['height']/2-$param['dy'], $Colors['Black'], 'Fonts/arial.ttf', $name);
        }

        $sideNames=[
            'U'=>[4.5,1.5,1,0,'Black'],
            'F'=>[4.5,4.5,1,1,'White'],
            'R'=>[7.5,4.5,2,1,'Black'],
            'L'=>[1.5,4.5,0,1,'Black'],
            'D'=>[4.5,7.5,1,2,'Black'],
            'B'=>[10.5,4.5,3,1,'Black'],
        ];

        imagesetthickness($im,1);
        foreach($sideNames as $name=>$coor){
            $X=$D*$coor[2]+$Border+$coor[0]*$Ceil;
            $Y=$D*$coor[3]+$Border+$coor[1]*$Ceil;
            imagefilledrectangle($im,$X-15, $Y-15, $X+15, $Y+15, $Colors[$Center[$name]['Color']]);
            imagerectangle($im,$X-15, $Y-15, $X+15, $Y+15, $Colors['Black']);
            $param=GetParam(12,'Fonts/Arial Bold.ttf', $name);
            imagefttext($im,12, 0, $X-$param['weith']/2-$param['dx'], $Y+$param['height']/2-$param['dy'], $Colors[$coor[4]], 'Fonts/Arial Bold.ttf', $name);
        }
    }

  return $im;
}
?>