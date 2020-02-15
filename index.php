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
    
    <link rel="stylesheet" href="<?= PageIndex(); ?>style.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex(); ?>fontawesome-free-5.12.0-web/css/all.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex(); ?>flag-icon-css/css/flag-icon.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex(); ?>icons-extra-event/css/Extra-Events.css?t=3" type="text/css"/>    
    <link rel="stylesheet" href="<?= PageIndex(); ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
    <script src="<?= PageIndex(); ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <script src="<?= PageIndex(); ?>jQuery/chosen_v1/chosen.jquery.js" type="text/javascript"></script>
    
    
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
                Speedcubing Extra Events
            </a>
        </div>
    </div>   
    
    <div class="content">    
        <?php IncludePage("Competitor.Login"); ?>
    </div>    
    <div class="content">
        <?php includePage('Navigator') ?>
        <?php IncludePage(RequestClass::getPage()); ?>
        
        <hr>
        <center>
        <a href="mailto:<?= urlencode(getini('Seniors','email')) ?>?subject=<?= ml('*.Title',false) ?>"><i class="far fa-envelope"></i> <?= ml('Footer.Contact.Delegates') ?></a>&nbsp;&nbsp;&nbsp;
        <a href="mailto:<?= urlencode(getini('Support','email')) ?>?subject=Support: <?= ml('*.Title',false) ?>"><i class="far fa-envelope"></i> <?= ml('Footer.Contact.Support') ?></a>&nbsp;&nbsp;&nbsp;
        <a href="<?= PageIndex()?>Icons"><i class="fas fa-image"></i> <?= ml('Footer.Icons') ?></a>&nbsp;&nbsp;&nbsp;    
        <a target="_blank" href="https://github.com/suphair/speedcubingextraevents.org"><i class="fab fa-github"></i> GitHub</a>&nbsp;&nbsp;&nbsp;
        </center>
    </div>    
    <?php IncludePage("Footer"); ?>
                      
    <?php add_visit(); ?>
</body>

<?php  #echo(DataBaseClass::getCount()); ?>

</html>   
