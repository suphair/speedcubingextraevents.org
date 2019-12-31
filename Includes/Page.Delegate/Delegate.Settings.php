<?php includePage('Navigator'); ?>
<?php $Delegate=ObjectClass::getObject('PageDelegate'); ?>

<h1 class="<?= $Delegate['Delegate_Status'] ?>">
    <img style="vertical-align: middle" width="40px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?>:
    <a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID'])?>"><?= short_Name($Delegate['Delegate_Name']) ?></a>
</h1>
<?php if($Delegate['Delegate_Contact']){ ?>
    <div class="block_comment"><?= Echo_format($Delegate['Delegate_Contact']) ?></div><br>
<?php } ?>
	<div class="form">
		<form method="POST" action="<?= PageAction("Delegate.Edit") ?>">
			<input name="ID" type="hidden" value="<?= $Delegate['Delegate_ID'] ?>" />
			<div class="form_field">
				Contacts
			</div>
			<div class="form_input">
				<textarea name="Contact"/><?= $Delegate['Delegate_Contact']?></textarea>
			</div>
			<div class="form_field">
				Status
                                <?php if(CheckAccess('Delegate.Settings.Ext')){ ?>
                                <span class="badge">ext</span>
                                <select name="Status">
                                    <?php foreach(['Senior','Middle','Junior','Trainee','Archive'] as $status){?>
                                        <option <?= $status==$Delegate['Delegate_Status']?'selected':'' ?> value="<?= $status ?>"><?= $status ?></option>
                                    <?php }?>
                                </select>
                                <?php } else{ ?>
                                    <?= $Delegate['Delegate_Status']?>
                                    <input hidden name="Status" value="<?= $Delegate['Delegate_Status']?>">
                                <?php } ?>
			</div>
                        
                        <div class="form_field">
				Secret
                                <input name="Secret" value="<?= $Delegate['Delegate_Secret'] ?>" />
			</div>
			<div class="form_change">
				<input type="submit" value="Change">
			</div>
		</form>
	</div>

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
    