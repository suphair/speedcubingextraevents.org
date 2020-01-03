<link rel="stylesheet" href="<?= PageLocal()?>style.css?t=1" type="text/css"/>
<style>*{font-family:Arial; !important}</style>
<?php if(CheckAccess('MainRegulations.Edit')){ ?>
    <img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png">   <a href="<?= PageIndex() ?>MainRegulations/Edit">Edit</a>
<?php } ?>
<form class='form_inline' method="POST" action="<?=PageAction('Language.Set')?> "> 
    <?php $Language=$_SESSION['language_select']; ?>    
    <?= ImageCountry($Language,20); ?>
    <select style="width:85px;" onchange="form.submit()" name='language'>
        <?php foreach(getLanguages() as $language){ ?>
        <option <?= $Language==$language?'selected':'' ?> value="<?= $language ?>"><?= CountryName($language,true) ?></option>
        <?php } ?>
    </select>
</form> 
<?php 
$regulations=GetBlockText("MainRegulation",$Language); 
echo Parsedown($regulations); ?>
