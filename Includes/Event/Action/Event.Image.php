<?php
RequestClass::CheckAccessExit(__FILE__, 'Event.Settings');

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];


if(!$_FILES['uploadfile']['error']){
    DataBaseClass::Query("Select CodeScript from `Discipline` where ID='$ID'");
    $Code=DataBaseClass::getRow()['CodeScript'];
    $filename= ImageEventFile($Code);  
    
    if($_FILES['uploadfile']['type'] == 'image/jpeg'){   
        if (exif_imagetype($_FILES['uploadfile']['tmp_name']) != IMAGETYPE_JPEG) {
            echo 'The picture is not a jpeg. Файл похож на jpeg, но это не jpeg';
            exit();
        }
        copy($_FILES['uploadfile']['tmp_name'],$filename);
    }elseif($_FILES['uploadfile']['type'] == 'image/png' ){
        $Image = imagecreatefrompng($_FILES['uploadfile']['tmp_name']);       
        imagejpeg($Image, $filename);            ;
    }
}    
SetMessage($_FILES['uploadfile']['error']);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
