<?php
session_start();
        
require_once 'file_utils.php';
RequireDir ('Classes');
RequireDir ('Functions');

error_reporting(E_ALL);
set_error_handler("myErrorHandler");

DataBaseInit ();
IncluderAction ();
IncluderScript ();

$languages=getLanguages();
if(!isset($_SESSION['language_select']) 
        or !in_array($_SESSION['language_select'],$languages)){    
    $_SESSION['language_select']=$languages[0];
}
RequestClass::setRequest();
if(RequestClass::getError(404)){ header('HTTP/1.0 404 not found'); }
if(RequestClass::getError(401)){ header('HTTP/1.1 401 Unauthorized'); } ?>

<!DOCTYPE HTML>
<html  lang="<?= $_SESSION['language_select'] ?>">
<head>
    <meta name="Description" content="Fun Cubing">
    <title><?= RequestClass::getTitle(); ?></title>
    <link rel="icon" href="<?= PageLocal()?>Logo/Logo_Color.png" >
    
    <link rel="stylesheet" href="<?= PageLocal()?>style.css?t=1" type="text/css"/>
    <link rel="stylesheet" href="<?= PageLocal()?>jQuery/chosen_v1/chosen.css" type="text/css"/>
    <script src="<?= PageLocal()?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <script src="<?= PageLocal()?>jQuery/chosen_v1/chosen.jquery.js" type="text/javascript"></script>
    
    
<style>
    body{
        background: linear-gradient(to bottom, <?= GetCompetitorData()?'#fdd,#fcc':'rgb(225,225,225),rgb(186,186,186)' ?>);
    }
</style>
</head>
<body>    
    <div class="header" style='clear:both; position: relative;'>
        <div style='float: left;'>
            <a href="<?= PageIndex(); ?>" class="title_link">
                <img class="logo" src="<?= PageIndex() ?>Logo/Logo_Color.png">
                <?= ml('*.Title') ?> 
            </a>
        </div>
        <?php if($Competitor= GetCompetitorData() and !$Competitor->avatar->is_default){ ?>
            <div style='float: right;'>
                <img class="logo" style="border-radius:20px"  src="<?= $Competitor->avatar->thumb_url ?>">
            </div>
        <?php } ?>
    </div>   
    <div class="content">    
        <?php IncludePage("Competitor.Login"); ?>
    </div>    
    <div class="content">
        <?php IncludePage(RequestClass::getPage()); ?>
    </div>    
    <?php IncludePage("Footer"); ?>
                      
    <div class="content">    
        <nobr><?= ml('Footer.Contact.Delegates') ?> <a href="mailto:<?= urlencode(getini('Seniors','email')) ?>?subject=<?= ml('*.Title',false) ?>"><?= getini('Seniors','email') ?></a></nobr>
        ▪ 
        <nobr><?= ml('Footer.Contact.Support') ?> <a href="mailto:<?= urlencode(getini('Support','email')) ?>?subject=Support: <?= ml('*.Title',false) ?>"><?= getini('Support','email') ?></a></nobr>
        ▪ 
        <nobr><a href="<?= PageIndex()?>Icons"><?= ml('Footer.Icons') ?></a></nobr>    
    </div>         
    <?php add_visit(); ?>
</body>

<?php  #echo(DataBaseClass::getCount()); ?>

</html>   
