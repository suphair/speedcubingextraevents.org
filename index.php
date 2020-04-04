<?php
session_start();

require_once 'file_utils.php';
RequireDir('Classes');
RequireDir('Classes/Data');
RequireDir('Classes/Object');
RequireDir('Functions');

error_reporting(E_ALL);
set_error_handler("myErrorHandler");

DataBaseInit();
IncluderAction();
IncluderScript();

$languages = getLanguages();
if (!isset($_SESSION['language_select'])
        or ! in_array($_SESSION['language_select'], $languages)) {
    $_SESSION['language_select'] = $languages[0];
}
RequestClass::setRequest();
if (RequestClass::getError(404)) {
    header('HTTP/1.0 404 not found');
}
if (RequestClass::getError(401)) {
    header('HTTP/1.1 401 Unauthorized');
}
?>

<!DOCTYPE HTML>
<html  lang="<?= $_SESSION['language_select'] ?>">
    <head>
        <meta name="Description" content="Speedcubing Extra Events">
        <meta charset="utf-8">
        <title><?= RequestClass::getTitle(); ?></title>
        <link rel="icon" href="<?= PageIndex() ?>Logo/Logo_Color.png" >
        <link rel="stylesheet" href="<?= PageIndex(); ?>style.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageIndex(); ?>fontawesome-free-5.12.0-web/css/all.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageIndex(); ?>flag-icon-css/css/flag-icon.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageIndex(); ?>icons-extra-event/css/Extra-Events.css" type="text/css"/>    
        <link rel="stylesheet" href="<?= PageIndex(); ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
        <script src="<?= PageIndex(); ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
        <script src="<?= PageIndex(); ?>jQuery/chosen_v1/chosen.jquery.js" type="text/javascript"></script>
    </head>
    <body> 
        <span id="variables" data-index="<?= PageIndex() ?>" data-title="<?= GetIni('TEXT', 'title') ?>"/>
        <?php IncludeClass::Page('Body'); ?>
    </body>
</html> 
<!-- start index.js -->
<script>
<?php include 'index.js' ?>
</script>    
<!-- end index.js -->
<?php trackVisitor(); ?>
