<h1><a href="<?= PageIndex()?>Delegate/Candidates">Applications to become a SEE Delegate</a> / Settings</h1>
<?php
$languages= getLanguages();
DataBaseClass::FromTable("RequestCandidateTemplate");
$templates=DataBaseClass::QueryGenerate();
$Language=$languages[0]; ?>
<table class="table_new">
    <thead>
    <tr>
        <td>Field</td>
        <td>Type</td>
        <td></td>
        <td></td>
    </tr>    
    </thead>
    <tbody>
<?php foreach($templates as $template){ ?>
    <tr>
        <form method="POST" action="<?= PageAction('Delegate.Candidates.Settings.Action')?>">
            <input type="hidden" name="Language" value="<?= $languages[0] ?>">
            <input type="hidden" name="ID" value="<?= $template['RequestCandidateTemplate_ID'] ?>">
            <input type="hidden" name="Action" value="Save">
        <td><input style="width:500px; font-size:16px;" required type="input" value="<?= $template['RequestCandidateTemplate_Name'] ?>"name="Name"</td>
        <td>
            <select name="Type">
                <option value="input" <?= $template['RequestCandidateTemplate_Type']=='input'?'selected':'' ?>>input</option>
                <option value="textarea" <?= $template['RequestCandidateTemplate_Type']=='textarea'?'selected':'' ?>>textarea</option>
            <select>
        </td>
        <td><button><i class="fas fa-save"></i> Save</button></td>
        </form>
        <form method="POST" action="<?= PageAction('Delegate.Candidates.Settings.Action')?>" onsubmit="return confirm('Confirm delete')">
            <input type="hidden" name="Language" value="<?= $languages[0] ?>">
            <input type="hidden" name="ID" value="<?= $template['RequestCandidateTemplate_ID'] ?>">
            <input type="hidden" name="Action" value="Delete">
        <td><button class="delete"><i class="fas fa-trash-alt"></i> Delete</button></td>
        </form>
    </tr>
<?php  } ?>
   <tr>
        <form method="POST" action="<?= PageAction('Delegate.Candidates.Settings.Action')?>">
          <input type="hidden" name="Language" value="<?= $languages[0] ?>">  
          <input type="hidden" name="Action" value="Add">
            <td><input style="width:500px; font-size:16px;" required type="input" value=""name="Name"</td>
        <td>
            <select name="Type">
                <option value="input" >input</option>
                <option value="textarea">textarea</option>
            <select>
        </td>
        <td><button><i class="fas fa-plus-square"></i> Add</button></td>
        </form>
    </tr> 
    <tbody>
</table>

<h3>The example of the request of candidate for SEE Delegates</h3>
<table class="table_info">
    <?php
    DataBaseClass::FromTable("RequestCandidateTemplate","Language='$Language'");
    $templates=DataBaseClass::QueryGenerate();
    if(!sizeof($templates)){
        DataBaseClass::FromTable("RequestCandidateTemplate","Language='EN'");    
        $templates=DataBaseClass::QueryGenerate();
    }

    foreach($templates as $template){ ?>
        <tr>
        <td>
            <?= $template['RequestCandidateTemplate_Name'] ?>
        </td>
        <td>
        <?php if($template['RequestCandidateTemplate_Type']=='input'){ ?>
            <input required name="Fields[<?= DataBaseClass::Escape($template['RequestCandidateTemplate_Name']) ?>]" value="" type="text">
        <?php }else{ ?>
                <textarea required name="Fields[<?= DataBaseClass::Escape($template['RequestCandidateTemplate_Name']) ?>]"></textarea>
        <?php } ?>
        </td>
        </tr>
    <?php } ?>        
    <tr>
        <td>Send an application</td>
        <td>
            <button><i class="fas fa-share-square"></i> Send</button>
        </td>
        <td/>
    </tr>
</form>
</table>

<?php 
$langs=array();
DataBaseClass::Query("Select distinct Language from RequestCandidateTemplate");
foreach(DataBaseClass::getRows() as $row){
    if(!in_array($row['Language'],$langs)){
    $langs[]=$row['Language'];
    }
}
