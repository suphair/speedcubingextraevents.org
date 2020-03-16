<?php
CheckPostIsset('Competition','Delegate','Comment');
CheckPostNotEmpty('Competition','Delegate');
CheckPostIsnumeric('Competition','Delegate');

$Competition=$_POST['Competition'];
$Delegate=$_POST['Delegate'];
RequestClass::CheckAccessExit(__FILE__, 'Competition.Report.Comment',$Competition);
$Comment= DataBaseClass::Escape($_POST['Comment']);

$CommentDelegate=getDelegate()['Delegate_ID'];

$Comment_ID=false;
DataBaseClass::Query("Select ID from CompetitionReportComment where Competition='$Competition' and Delegate='$Delegate' and CommentDelegate='$CommentDelegate'");
$row=DataBaseClass::getRow();
if(isset($row['ID'])){
    $Comment_ID=$row['ID'];
}

if(!trim($Comment)){
    DataBaseClass::Query("Delete from `CompetitionReportComment` where ID='$Comment_ID'");
}else{
    if($Comment_ID){
        DataBaseClass::Query("Update `CompetitionReportComment` set Comment='$Comment' where ID='$Comment_ID'");
    }else{
        DataBaseClass::Query("insert into `CompetitionReportComment` (Comment,Competition,Delegate,CommentDelegate) values ('$Comment',$Competition,$Delegate,$CommentDelegate)");
    }
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

