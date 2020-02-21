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
$Competitor= getCompetitor();
RequestClass::setRequest();
if(RequestClass::getError(404)){ header('HTTP/1.0 404 not found'); }
if(RequestClass::getError(401)){ header('HTTP/1.1 401 Unauthorized'); } ?>

<!DOCTYPE HTML>
<html  lang="<?= $_SESSION['language_select'] ?>">
<head>
    <meta name="Description" content="Fun Cubing">
    <title><?= RequestClass::getTitle(); ?></title>
    <link rel="icon" href="<?= PageIndex()?>Logo/Logo_Color.png" >
    <link rel="stylesheet" href="<?= PageIndex(); ?>style.css?t=5" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex(); ?>fontawesome-free-5.12.0-web/css/all.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex(); ?>flag-icon-css/css/flag-icon.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex(); ?>icons-extra-event/css/Extra-Events.css?t=3" type="text/css"/>    
    <link rel="stylesheet" href="<?= PageIndex(); ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
    <script src="<?= PageIndex(); ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <script src="<?= PageIndex(); ?>jQuery/chosen_v1/chosen.jquery.js" type="text/javascript"></script>
</head>
<body>    
    <table width='100%'>
        <tr>
            <td><img class="logo" src="<?= PageIndex() ?>Logo/Logo_Color.png"></td>
            <td class="header"><a href="<?= PageIndex(); ?>">Speedcubing Extra Events</a></td>
            <td  style='vertical-align: middle'>
                <form class='form_inline' method="POST" action="<?=PageAction('Language.Set')?> "> 
                    <?php if($Competitor){ ?>
                        <a href="#" onclick="
                            if($(this).hasClass('competitor_panel_link')){
                                $('.competitor-panel').show('fast');
                                $(this).addClass('competitor_panel_open_link');
                                $(this).removeClass('competitor_panel_link');
                            }else{
                                $('.competitor-panel').hide('fast');
                                $(this).addClass('competitor_panel_link');
                                $(this).removeClass('competitor_panel_open_link');
                            }
                            return false;
                            " class="local_link competitor_panel_link"><?= Short_Name($Competitor->name) ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?= PageIndex() ?>Actions/Competitor.Logout"><i class="fas fa-sign-out-alt"></i> <?= ml('Competitor.SignOut')?></a>&nbsp;
                    <?php }else{ ?>
                        <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?> 
                        <a href="<?= GetUrlWCA(); ?>"><i class="fas fa-sign-in-alt"></i> <?= ml('Competitor.SignIn')?></a>&nbsp;
                    <?php } ?>   
                    <?php $Language=$_SESSION['language_select']; ?>    
                    <?= ImageCountry($Language,20); ?>
                    <select style="width:85px;" onchange="form.submit()" name='language'>
                        <?php foreach(getLanguages() as $language){ ?>
                            <option <?= $Language==$language?'selected':'' ?> value="<?= $language ?>"><?= CountryName($language,true) ?></option>
                        <?php } ?>
                    </select>
                </form> 
            </td>
        </tr>
    </table>    
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
        <a href="<?= PageIndex()?>Export"><i class="fas fa-download"></i> <?= ml('Footer.Export') ?></a>&nbsp;&nbsp;&nbsp;
        </center>
    </div>     
    <?php trackVisitor(); ?>
</body>
</html>   
