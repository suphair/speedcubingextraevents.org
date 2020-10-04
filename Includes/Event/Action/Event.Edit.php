<?php
RequestClass::CheckAccessExit(__FILE__, 'Event.Settings');

CheckPostIsset('Name','Code','ID','Competitors','FormatResult','TNoodle','TNoodlesMult','Inspection','Comment','ScrambleComment');
CheckPostNotEmpty('Name','Code','ID','Competitors','FormatResult','TNoodlesMult','Inspection');
CheckPostIsNumeric('ID','Competitors','FormatResult','TNoodlesMult','Inspection');
$ID=$_POST['ID'];
$FormatResult=$_POST['FormatResult'];

$Formats_set=array();

if(isset($_POST['Formats']))
foreach($_POST['Formats'] as $format){
    if(!is_numeric($format)){
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();    
    }
    
    DataBaseClass::FromTable('Format',"ID=$format");
    if(DataBaseClass::rowsCount()==0){
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();    
    }
    
    $Formats_set[$format]=1;
}

$Comments= $_POST['Comment'];
foreach($Comments as $c=>$Comment){
    if(DataBaseClass::Escape($Comment)){
        $Comments[$c]=$Comment;
    }else{
        unset($Comments[$c]);
    }
}

$Name=$_POST['Name'];
$Code=$_POST['Code'];
$Competitors=$_POST['Competitors'];
$Inspection=$_POST['Inspection'];
$TNoodle= DataBaseClass::Escape($_POST['TNoodle']);
$TNoodles=[];
$TNoodlesMult=$_POST['TNoodlesMult'];
$ScrambleComment=DataBaseClass::Escape($_POST['ScrambleComment']);
if(isset($_POST['TNoodles'])){
    foreach($_POST['TNoodles'] as $code=>$tmp){
        $TNoodles[]=DataBaseClass::Escape($code);
    }
}
$TNoodles_str=implode(",",$TNoodles);


if(sizeof($TNoodles) or isset($_POST['GlueScrambles'])){
    $GlueScrambles=1;
}else{
    $GlueScrambles=0;
}

if( isset($_POST['CutScrambles'])){
    $CutScrambles=1;
}else{
    $CutScrambles=0;
}

if( isset($_POST['Simple'])){
    $Simple=1;
}else{
    $Simple=0;
}

DataBaseClass::Query("Update `Discipline` "
        . " set Name='$Name' , "
        . " Code='$Code' , "
        . " Competitors='$Competitors',"
        . " FormatResult='$FormatResult',"
        . " TNoodle='$TNoodle',"
        . " TNoodles='$TNoodles_str',"
        . " TNoodlesMult='$TNoodlesMult',"
        . " GlueScrambles=$GlueScrambles,"
        . " CutScrambles=$CutScrambles,"
        . " Simple=$Simple,"
        . " Inspection=$Inspection,"
        . " Comment='". DataBaseClass::Escape(json_encode($Comments,JSON_UNESCAPED_UNICODE))."', "
        . " ScrambleComment='$ScrambleComment'"
        . " where ID='$ID'");
DataBaseClass::FromTable('Discipline',"ID=$ID");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Format');
DataBaseClass::Join('DisciplineFormat','Event');
foreach(DataBaseClass::QueryGenerate() as $row){
    $Formats_set[$row['Format_ID']]=1;    
}
foreach(DataBaseClass::SelectTableRows('Format') as $row){
    if(isset($Formats_set[$row['Format_ID']])){
        DataBaseClass::Query("Select * from DisciplineFormat where Discipline=$ID and Format=".$row['Format_ID']);
        if(!DataBaseClass::rowsCount()){
            DataBaseClass::Query("Insert into DisciplineFormat (Discipline,Format) values ($ID,".$row['Format_ID'].")");   
        }
    }else{
        DataBaseClass::Query("Delete from DisciplineFormat where Discipline=$ID and Format=".$row['Format_ID']);
    }
        
}

SetMessage();

$link= PageIndex().'/Event/'.$Code.'/Settings';
header('Location: '.$link);
exit();  
