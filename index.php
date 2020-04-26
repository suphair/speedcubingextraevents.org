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
        <?= IncludeClass::Page('Index.Head.php') ?>
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
