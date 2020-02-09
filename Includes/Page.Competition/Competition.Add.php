<?php $Delegate= CashDelegate(); ?>
<h1><?= ml('Competition.Add') ?></h1>
<div class="wrapper">
    <div class="form"> 
        <form method="POST" action="<?= PageAction('Competition.Add') ?>">
            <div class="form_field">
                competition_id 
            </div>
            <div class="form_input">
                <input required type="text" name="WCA" value="" />
            </div>
            <div class="form_field">
                Delegate 
            </div>
            <div class="form_input">
                <?php if(CheckAccess('Competition.Add.Ext')){ ?>
                <span class='badge'> Ext 
                    <select name="Delegate">
                        <?php foreach(DataBaseClass::SelectTableRows('Delegate') as $delegate_row){ ?>
                            <option  <?= $delegate_row['Delegate_ID']==$Delegate['Delegate_ID']?'selected':'' ?> value="<?= $delegate_row['Delegate_ID'] ?>">
                                <?= $delegate_row['Delegate_Status']=='Archive'?'- ':'' ?>
                                <?= $delegate_row['Delegate_Name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </span>    
                <?php }else{ ?>
                    <?= $Delegate['Delegate_Name'] ?>
                <?php } ?>
            </div>
            <div class="form_enter">
                <input type="submit" value="Create">
            </div>
        </form>
        <?php $err=GetMessage("CompetitionCreate");
        if($err){ ?>
            <br><span class="error"><?= $err?></span>
        <?php } ?>
    </div>
</div>