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
    
        <h1>Send an application to become a SEE Delegate</h1>
        <?php if(!$competitor){ ?>
            <i class="fas fa-sign-in-alt"></i> <a href="<?= GetUrlWCA(); ?>"><?= ml('Competitor.SignIn') ?></a>
        <?php }elseif(!$competitor->wca_id){ ?>
                    <h2><i class="fas fa-hand-paper"></i> You must have a WCA ID</h2>
           <?php }elseif(sizeof($RequestCandidateFields) and $RequestCandidateFields[0]['RequestCandidate_Status']==-1){ ?>
                    <h2><i class="fas fa-hand-paper"></i> Your request was rejected, you can try again in a year (after <?= date_range(date('Y-m-d',strtotime($RequestCandidateFields[0]['RequestCandidate_Datetime']." +1 year "))) ?>)</h2>
            <?php }else{ ?>
                <form method="POST" action="<?= PageAction('Delegate.Candidate.Add')?>">
                    <input type="hidden" name='ID' value="<?= $competitor->id ?>">
                    <table class="table_info">
                    <?php if($competitor->delegate_status){ 
                    $delegate_block= GetBlockText('DelegateWCA.Candidate'); ?>
                    <?php if($delegate_block){ ?>
                        <tr>
                            <td><i class="fas fa-info-circle"></i> Info for WCA Delegate</td>
                            <td><?php Parsedown($delegate_block) ?></td>
                    <?php } ?>
                    <?php } ?>
                        
                        
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
                    </tr>
                </form>
                </table>        
        <?php } ?>           

<?php if(isset($RequestCandidateFields) and sizeof($RequestCandidateFields)){ ?>
    <h3>Your application was sent at <?= $RequestCandidateFields[0]['RequestCandidate_Datetime']?></h3>
    <table class="table_info">
    <?php foreach($RequestCandidateFields as $RequestCandidateField){ ?>
        <tr>
            <td><?= $RequestCandidateField['RequestCandidateField_Field'] ?></td>
            <td><?= $RequestCandidateField['RequestCandidateField_Value'] ?></td>
        </tr>  
    <?php } ?> 
    </table>
<?php } ?>
<?= mlb('Delegate.Candidate.SignIn') ?>
<?= mlb('Delegate.Candidate.Save') ?>
<?= mlb('Delegate.Candidate.Sent') ?>
<?= mlb('Delegate.Candidate.noWCAID') ?>
<?= mlb('Delegate.Candidate.Declined') ?>