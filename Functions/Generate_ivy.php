<?php

function Generate_ivy(){
    do{
        $solve=GenerateIvyAttempt(); 
    }while(!$solve);
    return $solve;    
}

function GenerateIvyAttempt(){
    $Corners=array(
        "U"=>rand(-1,1),
        "F"=>rand(-1,1),
        "R"=>rand(-1,1),
        "L"=>rand(-1,1)
    );
    
    $positionCenters=array("U","R","D","L"," "," ");
    $Centers=array("U"=>" ","R"=>" ","r"=>" ","L"=>" ","l"=>" ","D"=>" ");

    
    foreach($Centers as $c=>$tmp){
        $r=array_rand($positionCenters);
        $Centers[$c]=$positionCenters[$r];
        unset($positionCenters[$r]);
    }
    
    if(CheckSolveIvy($Corners,$Centers)){
        return false;
    }
    $max_depth=7;
    $Cut=rand(6,7);
    $min_solve=3;
    for($d=1;$d<=$min_solve;$d++){
        $IvyHelperAlgs= file("IvyHelper/algs$d.txt",true);
        $IvyHelperPrimes= file("IvyHelper/primes$d.txt",true);

        foreach($IvyHelperAlgs as $alg){
            foreach($IvyHelperPrimes as $prime){    
                $solver=array();
                for($i=0;$i<$d;$i++){
                    $solver[]=substr($alg,$i,1).substr($prime,$i,1);
                }
                
                if(SolverIvy($Corners,$Centers,$solver)){
                    return false;
                }  
            }
        }
    }
    
    for($d=$Cut;$d<=$max_depth;$d++){
        $IvyHelperAlgs= file("IvyHelper/algs$d.txt",true);
        $IvyHelperPrimes= file("IvyHelper/primes$d.txt",true);

        foreach($IvyHelperAlgs as $alg){
            foreach($IvyHelperPrimes as $prime){    
                $solver=array();
                for($i=0;$i<$d;$i++){
                    $solver[]=substr($alg,$i,1).substr($prime,$i,1);
                }
                
                if(SolverIvy($Corners,$Centers,$solver)){
                    return(implode(" ",$solver));
                }  
            }
        }
    }
}