<?php includePage('Navigator'); ?>
<?php
    $competitor= GetCompetitorData(); 
    $Language=$_SESSION['language_select'];
    if($competitor){    
        DataBaseClass::fromTable("RequestCandidateField");
        DataBaseClass::Join_current("RequestCandidate");
        DataBaseClass::Join_current("Competitor");
        DataBaseClass::Where_current("WID=".GetCompetitorData()->id);
        DataBaseClass::OrderClear("RequestCandidateField", "ID");
        $RequestCandidateFields=DataBaseClass::QueryGenerate();
    } ?>    
    
        <h1><?= ml('Delegate.Candidate.Title') ?></h1>
        <?php if(!$competitor){ ?>
            <span class="error"><?= ml('Delegate.Candidate.SignIn') ?> <a href="<?= GetUrlWCA(); ?>"><?= ml('Competitor.SignIn') ?></a></span>
        <?php }elseif(!$competitor->wca_id){ ?>
                    <span class="error"><?= ml('Delegate.Candidate.noWCAID') ?></span>  
           <?php }elseif(sizeof($RequestCandidateFields) and $RequestCandidateFields[0]['RequestCandidate_Status']==-1){ ?>
                    <span class="error"><?= ml('Delegate.Candidate.Declined', date_range(date('Y-m-d',strtotime($RequestCandidateFields[0]['RequestCandidate_Datetime']." +1 year ")))) ?></span><br>       
            <?php }else{ ?>
            <?php if($competitor->delegate_status){ 
                $delegate_block= GetBlockText('DelegateWCA.Candidate'); ?>
                    <?php if($delegate_block){ ?>
                        <div class="form2"><?= Parsedown($delegate_block,false) ?></div>
                    <?php } ?>
            <?php } ?>
                    
            <h2><?= $competitor->name; ?> &#9642; <?= $competitor->wca_id; ?> &#9642; <?= CountryName($competitor->country_iso2) ?></h2>
            <div class='form'>
                <form method="POST" action="<?= PageAction('Delegate.Candidate.Add')?>">
                    <input type="hidden" name='ID' value="<?= $competitor->id ?>">
                    <?php
                    DataBaseClass::FromTable("RequestCandidateTemplate","Language='$Language'");
                    $templates=DataBaseClass::QueryGenerate();
                    if(!sizeof($templates)){
                        DataBaseClass::FromTable("RequestCandidateTemplate","Language='EN'");    
                        $templates=DataBaseClass::QueryGenerate();
                    }
                    
                    foreach($templates as $template){ ?>
                        <div class="form_field">
                            <?= $template['RequestCandidateTemplate_Name'] ?>
                        </div>
                    <div class="form_input">
                        <?php if($template['RequestCandidateTemplate_Type']=='input'){ ?>
                            <input required name="Fields[<?= DataBaseClass::Escape($template['RequestCandidateTemplate_Name']) ?>]" value="" type="text">
                        <?php }else{ ?>
                                <textarea required name="Fields[<?= DataBaseClass::Escape($template['RequestCandidateTemplate_Name']) ?>]"></textarea>
                        <?php } ?>
                    </div>
                    <?php } ?>        
                    <div class="form_change">
                        <input type="submit" value="<?= ml('Delegate.Candidate.Save',false) ?>">
                    </div>
                </form>
                <?php $err=GetMessage("RequestCandidateAdd");
                    if($err){ ?>
                        <br><span class="error"><?= $err?></span>
                    <?php } ?>
            </div>
        <?php } ?>           
<br>
<?php if(isset($RequestCandidateFields) and sizeof($RequestCandidateFields)){ ?>
<h2><?= ml('Delegate.Candidate.Sent') ?> <?= $RequestCandidateFields[0]['RequestCandidate_Datetime']?></h2>

<?php foreach($RequestCandidateFields as $RequestCandidateField){ ?>
    <p>
        <?= $RequestCandidateField['RequestCandidateField_Field'] ?> &#9642;
        <?= $RequestCandidateField['RequestCandidateField_Value'] ?>
    </p>  
<?php } 
} ?>
<?= mlb('Delegate.Candidate.SignIn') ?>
<?= mlb('Delegate.Candidate.Save') ?>
<?= mlb('Delegate.Candidate.Sent') ?>
<?= mlb('Delegate.Candidate.noWCAID') ?>
<?= mlb('Delegate.Candidate.Declined') ?>