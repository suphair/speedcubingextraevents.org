<?php $Delegate=ObjectClass::getObject('PageDelegate'); ?>

<h1 class="<?= $Delegate['Delegate_Status'] ?>">
    <a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID'])?>"><?= short_Name($Delegate['Delegate_Name']) ?></a> / Settings
</h1>
<table class="table_info">
<?php if($Delegate['Delegate_Contact']){ ?>    
    <tr>
        <td>Contacts to display</td>
        <td><?= Parsedown($Delegate['Delegate_Contact']) ?></td>
    </tr>    
<?php } ?>    
    <form method="POST" action="<?= PageAction("Delegate.Edit") ?>">
    <input name="ID" type="hidden" value="<?= $Delegate['Delegate_ID'] ?>" />
    <tr>
        <td>Contacts</td>
        <td><textarea name="Contact"/><?= $Delegate['Delegate_Contact']?></textarea></td>
    </tr>   
    <?php if(CheckAccess('Delegate.Settings.Ext')){ ?>
    <tr>
        <td>Status <i class="fas fa-crown"></i></td>
        <td>
            <select name="Status">
                <?php foreach(['Senior','Middle','Junior','Trainee','Archive'] as $status){?>
                    <option <?= $status==$Delegate['Delegate_Status']?'selected':'' ?> value="<?= $status ?>"><?= $status ?></option>
                <?php }?>
            </select>
        </td>
    </tr>    
    <?php } else{ ?>
    <tr>
        <td>Status</td>
        <td>
                <?= $Delegate['Delegate_Status']?>
                <input hidden name="Status" value="<?= $Delegate['Delegate_Status']?>">
        </td>
    </tr>  
            <?php } ?>
    <tr>
        <td>Secret for alternative login</td>
        <td><input name="Secret" value="<?= $Delegate['Delegate_Secret'] ?>" /></td>
    </tr>  
    <tr>
        <td></td>
        <td><button><i class="far fa-save"></i> Save</button></td>
    </tr>  
    </form>
</table>

<?php
    DataBaseClass::FromTable("CompetitionDelegate", "Delegate='".$Delegate['Delegate_ID']."'");
    DataBaseClass::Join_current("Competition");
    DataBaseClass::QueryGenerate();
    if (DataBaseClass::rowsCount()==0){ ?>
        <div class="form">
                <form method="POST" action="<?= PageAction("Delegate.Delete") ?>"   onsubmit="return confirm('Attention: Confirm the deletion.')">
                    <input name="ID" type="hidden" value="<?= $Delegate['Delegate_ID'] ?>" />
                    <input class="delete" type="submit" value="Delete">
                </form>
        </div>
    <?php } ?> 
    