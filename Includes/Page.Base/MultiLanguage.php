<?php
$Languages=getLanguages();
if(isset($_SERVER['HTTP_REFERER']) and !isset($_SESSION['ML_ACTION'])){
    $_SESSION['HTTP_REFERER']=$_SERVER['HTTP_REFERER'];
}
unset($_SESSION['ML_ACTION']);
$Link=$_SESSION['HTTP_REFERER'];?>
<h1>Multi Language</h1>
<h3><a href='<?= $Link ?>'><?= $Link ?></a></h3>
<?php
$MultiLanguages=[];

if(strpos($Link,'MultiLanguage')!==false){
    $ml_finds=[]; 
    DataBaseClass::Query("Select distinct Name from MultiLanguage order by Name ");
    foreach(DataBaseClass::getRows() as $row){
        $ml_finds[]=$row['Name'];
    }
}else{
    $content= file_get_contents_curl_PHPSESSID($Link);
    preg_match_all('/<!--ML\[(.+?)\]-->/ism', $content, $matches);
    $ml_finds=$matches[1];
}


DataBaseClass::Query("Select * from MultiLanguage order by Name, Language ");
foreach(DataBaseClass::getRows() as $row){
    $MultiLanguages[$row['Name']][ $row['Language']]=htmlentities($row['Value']);
} 

foreach($MultiLanguages as $name=>$tmp){
    foreach($Languages as $Language){   
        if(!isset($MultiLanguages[$name][$Language])){
            $MultiLanguages[$name][$Language]="";
        }
    }    
}
$MultiLanguagesOut=[];
foreach($ml_finds as $ml_find){
    if(!isset($MultiLanguages[$ml_find])){
        foreach($Languages as $Language){       
            $MultiLanguages[$ml_find][$Language]="";
        }
    }
    foreach($Languages as $Language){   
        $MultiLanguagesOut[$ml_find][$Language]=$MultiLanguages[$ml_find][$Language];      
    }
}
ksort($MultiLanguagesOut);
?>
<form method="POST" action="<?= PageAction('Language.Edit') ?>">
<table class="table_new">
    <thead>
    <tr>
        <td>Name</td>
        <?php foreach($Languages as $Language){ ?>
            <td>
                <?= ImageCountry($Language); ?> <?= CountryName($Language,true); ?>
            </td>
        <?php } ?>
    </tr> 
    </thead>
<?php foreach($MultiLanguagesOut as $Name=>$MultiLanguages){ ?>
    <tr>
        <td>
            <?= $Name; ?>
        </td>
        <?php foreach($Languages as $Language){ ?>
            <td width='400'>
                <input size='60px' name='MultiLanguages[<?= $Name ?>][<?= $Language ?>]' value='<?= $MultiLanguagesOut[$Name][$Language] ?>'/>
            </td>
        <?php } ?>
     </tr>   
<?php } ?>
</table>
    <button><i class="fas fa-save"></i> Save</button>
</form>