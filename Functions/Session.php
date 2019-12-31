<?php

function TakeSession($name){
    $return="";
    if(isset($_SESSION[$name])){
        $return=$_SESSION[$name];
        unset($_SESSION[$name]);
    }
    
    return $return;
}

function SetMessage($str="Complete"){
    if(isset($_POST['Password'])){
        $_POST['Password']="***";
    }

    $_SESSION['message']=$str.": ".basename(debug_backtrace()[0]['file'])." POST:".print_r($_POST,true);
}

function SetMessageName($name,$str){
    $_SESSION[$name]=$str;
}

function GetMessage($name="message"){
    $str="";
    if(isset($_SESSION[$name])){
        $str=$_SESSION[$name];
        unset($_SESSION[$name]);
    }
    return $str;  
}

function SetPostValues($action){   
    foreach($_POST as $name=>$value){
        $_SESSION["POST"][$action][$name]=$value;
    }
    
    if(isset($_SESSION['Competitor']->id)){
        $competitor= json_encode(['id'=>$_SESSION['Competitor']->id,'name'=>$_SESSION['Competitor']->name]);
    }else{
        $competitor="";    
    }
    
    $request= DataBaseClass::Escape(json_encode(getRequest()));
    $post= DataBaseClass::Escape(json_encode($_POST));
    DataBaseClass::Query("Insert into LogsPost (Post,Competitor,Request) values ('$post','$competitor','$request')");   
}

function GetPostValues($action,$name){   
    if(!isset($_SESSION["POST"][$action][$name])){
        return false;
    }else{
        return $_SESSION["POST"][$action][$name];
    }
}