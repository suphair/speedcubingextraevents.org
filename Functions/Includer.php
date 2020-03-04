<?php
function IncludePage($file){
    if(!$file)return;
    foreach(scandir('Includes') as $dir){
        if(strpos($dir,'Page')!==false){
           $arr[]="Includes/$dir/$file.php"; 
        }
    }     
    if(!IncludeExists($arr)){
        #the visitor does not see this
        echo ">>Error Page ".$file."<<";
        exit();
    }
}

function IncluderAction(){
    $request=getRequest();   
    if(sizeof($request)>=2){
        $file=$request[1];
        if($request[0]=="crons"){
            if(!(CheckAccess('Scripts') or $_SERVER['HTTP_USER_AGENT']=='Wget/1.17.1 (linux-gnu)' or strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false)){
                header('HTTP/1.1 401 Unauthorized');
                echo "<a href='".PageIndex()."'>".GetIni('TEXT', 'title')."</a>";
                echo "<h1 style='color:red'>You do not have permission to run [".$file."] cron script</h1>";
                exit();
            }else{
                if(!IncludeExists("Includes/Crons/$file.php")){
                    header('HTTP/1.0 404 not found');
                    echo "<a href='".PageIndex()."'>".GetIni('TEXT', 'title')."</a>";
                    echo "<h1 style='color:red'>Сron script [".$file."] is not found</h1>";
                    exit();
                }
                      
            }
        }
        
        if(strtolower($request[0])=="actions"){
            SetPostValues($request[1]);
            foreach(scandir('Includes') as $dir){
                if(strpos($dir,'Action')!==false){
                   $arr[]="Includes/$dir/$file.php"; 
                }
            } ;
            if(!IncludeExists($arr)){
                header('HTTP/1.0 404 not found');
                echo "<a href='".PageIndex()."'>".GetIni('TEXT', 'title')."</a>";
                echo "<h1 style='color:red'>The action [".$file."] is not found </h1>";
                exit();
            }
        }
    }
}

function IncludeExists($file){
    if(is_array($file)){
        foreach($file as $f){
            if(file_exists($f)){
                include $f;
                return true;
            }
        } 
        return false;
    }else{    
        if(file_exists($file)){
            include $file;
            return true;
        }else{
            return false;
        }
    }
    
}

function getRequest(){
    
    global $request;
    $request_=explode('?',$_SERVER['REQUEST_URI'])[0];
    $request=explode("/",str_replace("/".getIni("LOCAL","PageBase")."/","/",$request_));
    unset($request[0]);
    foreach($request as $n=>$v){
        if(!$v) unset($request[$n]);
    }
    $request=array_values($request);
    if(isset($request[0]) and $request[0]!='Actions'){
        foreach($request as $k=>$v){
            $request[$k]= DataBaseClass::Escape(strtolower($v));
        }
    }
    return $request;
}

function getRequestString(){
    
    return str_replace("/".getIni("LOCAL","PageBase")."/","/",$_SERVER['REQUEST_URI']);
}

function Request(){
    global $request;
    return $request;
}

function IncluderScript(){
    if(isset($_GET['Script'])){
        if(CheckAccess('Scripts')){
            $Script="Script_{$_GET['Script']}";
            echo "[$Script]";
            if(function_exists($Script)){
                echo "Found ";
                eval("$Script();");
                echo "Complete ";
            }else{
                echo "Not found";
            }
            exit();
        }
    }
}