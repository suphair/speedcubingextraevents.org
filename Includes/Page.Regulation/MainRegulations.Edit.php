<link rel="stylesheet" href="<?= PageLocal()?>style.css?t=1" type="text/css"/>
<a href="<?= PageIndex() ?>MainRegulations">MainRegulations</a>
<style>
    h3:before{content:none}
</style>
<form class='form_inline' method="POST" action="<?=PageAction('Language.Set')?> "> 
    <?php $Language=$_SESSION['language_select']; ?>    
    <?= ImageCountry($Language,20); ?>
    <select style="width:85px;" onchange="form.submit()" name='language'>
        <?php foreach(getLanguages() as $language){ ?>
        <option <?= $Language==$language?'selected':'' ?> value="<?= $language ?>"><?= CountryName($language,true) ?></option>
        <?php } ?>
    </select>
</form> 
<?php $regulations=GetBlockText("MainRegulation",$Language); ?>
<table width="100%">
    <tr>
        <td width="50%" valign="top" style="padding:10px">
            <form method="POST" action="<?= PageAction('MainRegulation.Edit')?>">
                <input hidden value="<?= $Language ?>" name="language">    
                <textarea name="text" style="width: 100%;height: 600px; padding:10px; font-size:14px;"><?= $regulations ?></textarea>
                <p><button>Save</button></p>
            </form>
        </td>   
        <td width="50%" valign="top" style="padding:10px">
            <div style='height: 600px;overflow: scroll;border:1px dotted black; padding:10px;'>        
            <?php 
            $regulations=str_replace(chr(13).chr(10),"\n\n",$regulations);
            $regulations=str_replace("\n\n\n\n","\n\n&nbsp;\n\n",$regulations);
             Parsedown($regulations); ?>
            </div>
        </td>   
    </tr>
</table>