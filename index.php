<?php
session_start();

require_once 'file_utils.php';
RequireDir('Classes');
RequireDir('Classes/Data');
RequireDir('Classes/Object');
RequireDir('Functions');

Suphair \ Config :: init('Config');
Suphair \ Error :: register(Suphair \ Config :: isLocalhost());

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
        <?php IncludeClass::Page('Index.Head'); ?>
    </head>
    <body> 
        <span id="variables" data-index="<?= PageIndex() ?>" data-title="Speedcubing Extra Events"/>
        <?php IncludeClass::Page('Body'); ?>
    </body>
</html> 
<!-- start index.js -->
<script>
<?php include 'index.js' ?>
</script>    
<!-- end index.js -->

<?php trackVisitor(); ?>
<?php DataBaseClass::close(); ?>
