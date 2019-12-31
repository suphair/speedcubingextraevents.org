<h1><?= ml('*.Settings') ?>: <a href="<?= PageIndex()?>Delegate/Candidates"><?= ml('Delegate.Candidates.Title') ?></a></h1>
<?php
$languages= getLanguages();
DataBaseClass::FromTable("RequestCandidateTemplate");
$templates=DataBaseClass::QueryGenerate();?>
<table>
<?php foreach($templates as $template){ ?>
    <tr>
        <form method="POST" action="<?= PageAction('Delegate.Candidates.Settings.Action')?>">
            <input type="hidden" name="ID" value="<?= $template['RequestCandidateTemplate_ID'] ?>">
            <td>
                <?= ImageCountry($template['RequestCandidateTemplate_Language'], 20)?>
                <select name="Language" style="width:80px">
                    <?php foreach($languages as $language){ ?>
                        <option value='<?= $language ?>' <?= $template['RequestCandidateTemplate_Language']==$language?'selected':'' ?>>
                            <?= CountryName($language, true) ?>
                        </option>
                    <?php } ?>
                 </select>   
            </td>    
        <td><input style="width:500px; font-size:16px;" required type="input" value="<?= $template['RequestCandidateTemplate_Name'] ?>"name="Name"</td>
        <td>
                <input id="TypeInput<?= $template['RequestCandidateTemplate_ID'] ?>" <?= $template['RequestCandidateTemplate_Type']=='input'?'checked':'' ?> type="radio" name="Type" value="input">
                <label for="TypeInput<?= $template['RequestCandidateTemplate_ID'] ?>"><?= ml('Delegate.Request.Settings.Input') ?></label><br>
                <input id="TypeTextarea<?= $template['RequestCandidateTemplate_ID'] ?>" <?= $template['RequestCandidateTemplate_Type']=='textarea'?'checked':'' ?>  type="radio" name="Type" value="textarea">
                <label for="TypeTextarea<?= $template['RequestCandidateTemplate_ID'] ?>"><?= ml('Delegate.Request.Settings.Textarea') ?></label><br>
        </td>
        <td><input type="submit" name="Action" value="<?= ml('*.Save',false) ?>"></td>
        <td><input class="delete" type="submit" name="Action" value="<?= ml('*.Delete',false) ?>"></td>
        </form>
    </tr>
<?php  } ?>
   <tr>
        <form method="POST" action="<?= PageAction('Delegate.Candidates.Settings.Action')?>">
            <td>
                <select name="Language" style="width:80px">
                    <?php foreach($languages as $language){ ?>
                        <option value='<?= $language ?>'>
                            <?= CountryName($language, true) ?>
                        </option>
                    <?php } ?>
                 </select>   
            </td>    
            <td><input style="width:500px; font-size:16px;" required type="input" value=""name="Name"</td>
        <td>
                <input id="TypeInput0" checked  type="radio" name="Type" value="input">
                <label for="TypeInput0"><?= ml('Delegate.Request.Settings.Input') ?></label><br>
                <input id="TypeTextarea0"  type="radio" name="Type" value="textarea">
                <label for="TypeTextarea0"><?= ml('Delegate.Request.Settings.Textarea') ?></label><br>
        </td>
        <td><input  type="submit" name="Action" value="<?= ml('*.Add',false) ?>"></td>
        </form>
    </tr> 
    
</table>
<?php 
$langs=array();
DataBaseClass::Query("Select distinct Language from RequestCandidateTemplate");
foreach(DataBaseClass::getRows() as $row){
    if(!in_array($row['Language'],$langs)){
    $langs[]=$row['Language'];
    }
}

foreach($langs as $lang){ 
    DataBaseClass::FromTable("RequestCandidateTemplate","Language='".$lang."'");
    $templates=DataBaseClass::QueryGenerate();
    ?>
<h2><?= ImageCountry($lang, 30)?> <?= CountryName($lang, true) ?> ▪ <?= ml('Delegate.Candidates.Setting.Example')?></h2>
    <div class='form'>
        <input type="hidden" name='ID' value="<?= $competitor->id ?>">
        <?php
        foreach($templates as $template){ ?>
            <div class="form_field">
                <?= $template['RequestCandidateTemplate_Name'] ?>
            </div>
        <div class="form_input">
            <?php if($template['RequestCandidateTemplate_Type']=='input'){ ?>
                <input  type="text">
            <?php }else{ ?>
                    <textarea></textarea>
            <?php } ?>
        </div>
        <?php } ?>        
        <div class="form_change">
            <input type="submit" value="Подать заявку">
        </div>
     </div>
<?php } ?>    

<?= mlb('*.Add') ?>
<?= mlb('*.Delete') ?>
<?= mlb('*.Save') ?>