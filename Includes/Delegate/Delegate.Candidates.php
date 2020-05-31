<h1>Candidates for delegates</h1>
<?php if (CheckAccess('Delegate.Candidate.GenerateCode')) { ?>
    <h3>
        Generating a link to create an application
    </h3>
    <form method="POST" action="<?= PageAction("Delegate.GenerateCode") ?>">
        WCA ID
        <input name="WCAID" placeholder="WCA ID">
        <button>
            <i class="fas fa-link"></i>
            Generate 
        </button>
        <p>
            <?= GetMessage('Candidate.GenerateCode'); ?>
        </p>
    </form>
<?php } ?>


<?php if (CheckAccess('Delegate.Candidates.Settings')) { ?>
    <table class="table_info">
        <tr>
            <td><i class="fas fa-cog"></i></td>
            <td><a href="<?= PageIndex() ?>Delegate/Candidates/Settings"> Settings application</a></td>
        <tr>
    </table>
<?php } ?>
<?php
$Delegate = getDelegate();
$CheckAccessVote = CheckAccess('Delegate.Candidate.Vote');
$CheckAccessDecline = CheckAccess('Delegate.Candidate.Decline');
$CheckAccessAccept = CheckAccess('Delegate.Candidate.Accept');

DataBaseClass::Query("Select RC.Competitor, coalesce(RCV.Status,-2) Status ,RCV.Delegate,D.Name,RCV.Reason  from RequestCandidate RC "
        . " join RequestCandidateVote RCV on RCV.Competitor=RC.Competitor"
        . " join Delegate D on D.ID=RCV.Delegate order by D.Name");

$RequestCandidateVote = [];
$RequestCandidateVoteReason = [];
$RequestCandidateVoteReasons = [];
foreach (DataBaseClass::getRows() as $row) {
    $RequestCandidateVote[$row['Competitor']][$row['Delegate']] = $row['Status'];
    $RequestCandidateVoteReason[$row['Competitor']][$row['Delegate']] = $row['Reason'];
    $RequestCandidateVoteReasons[$row['Competitor']][] = $row;
} 
DataBaseClass::Query("Select * from Delegate where status='Senior' order by Name");
$Seniors = DataBaseClass::getRows();


DataBaseClass::FromTable("RequestCandidate");
DataBaseClass::Join_current("Competitor");
DataBaseClass::OrderClear("RequestCandidate", "Status desc");
DataBaseClass::Order("RequestCandidate", "ID desc");
$RequestCandidates = DataBaseClass::QueryGenerate();
DataBaseClass::Join("RequestCandidate", "RequestCandidateField");
DataBaseClass::Order("RequestCandidateField", "ID");
$RequestCandidateFields = DataBaseClass::QueryGenerate();
?>

<?php foreach ([0, -1, 1] as $status) { ?>

    <h3>
        <?php if ($status == 0) { ?>Applications in processing<?php } ?>
        <?php if ($status == -1) { ?>Rejected applications<?php } ?>
        <?php if ($status == 1) { ?>Accepted applications<?php } ?>

    </h3>
    <table width="100%"><tr><td width="20%">
                <table class="table_new">
                    <thead>
                        <tr>
                            <?php foreach ($Seniors as $senior) { ?>
                                <td><?= substr($senior['Name'], 0, 1) ?></td>
                            <?php } ?>
                            <td><span style="visibility: hidden" class="list_select"></span></td>
                            <td>Name</td>
                            <td>Country</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($RequestCandidates as $RequestCandidate)
                            if ($RequestCandidate['RequestCandidate_Status'] == $status) {
                                ?>
                                <tr>
                                    <?php foreach ($Seniors as $senior) { ?>
                                        <td>
                                            <?php
                                            $vote = -3;
                                            if (isset($RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$senior['ID']])) {
                                                $vote = $RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$senior['ID']];
                                                if ($vote == 1) {
                                                    ?>
                                                    <i class="fas fa-thumbs-up"></i>
                                                    <?php
                                                }
                                                if ($vote == -1) {
                                                    ?>
                                                    <i class="fas fa-ban"></i>
                                                    <?php
                                                }
                                                if ($vote == 0) {
                                                    ?>
                                                    <i class="fas fa-balance-scale"></i></span>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    <td><span class="RequestCandidateSelect" ID="RequestCandidateSelect_<?= $RequestCandidate['RequestCandidate_ID'] ?>" ></span></td>
                                    <td><a href="#"
                                           onclick="
                                                   $('.RequestCandidate').hide('fast');
                                                   $('#RequestCandidate_<?= $RequestCandidate['RequestCandidate_ID'] ?>').show('fast');
                                                   $('.RequestCandidateSelect').removeClass('list_select');
                                                   $('#RequestCandidateSelect_<?= $RequestCandidate['RequestCandidate_ID'] ?>').addClass('list_select');
                                                   return false;" class="local_link"><?= $RequestCandidate['Competitor_Name'] ?></a></td>       
                                    <td><?= CountryName($RequestCandidate['Competitor_Country']); ?></td>       
                                </tr>    
                            <?php } ?>
                    </tbody>    
                </table>    
            </td><td width="80%">
                <?php
                foreach ($RequestCandidates as $RequestCandidate)
                    if ($RequestCandidate['RequestCandidate_Status'] == $status) {
                        ?>    
                        <span class="RequestCandidate" ID="RequestCandidate_<?= $RequestCandidate['RequestCandidate_ID'] ?>" style="display:none">    
                            <p><b>Name</b> <?= $RequestCandidate['Competitor_Name'] ?></p>
                            <p><b>Country</b> <?= ImageCountry($RequestCandidate['Competitor_Country']) ?> <?= CountryName($RequestCandidate['Competitor_Country']) ?></p>
                            <p><b>Date</b> <?= date_range(date('Y-m-d', strtotime($RequestCandidate['RequestCandidate_Datetime']))); ?></p>
                            <p><b>Invited by</b> <?= implode(",", DataBaseClass::getColumn("Select distinct Delegate from CandidateCode where Candidate='{$RequestCandidate['Competitor_WCAID']}'")); ?></p>
                            <p><b>WCA Profile</b> <a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $RequestCandidate['Competitor_WCAID'] ?>"><?= $RequestCandidate['Competitor_WCAID'] ?> <i class="fas fa-external-link-alt"></i></a></p>
                            <p><b>SEE Profile</b> <a target="_blank" href="<?= PageIndex() . "Competitor/" . $RequestCandidate['Competitor_WCAID'] ?>">SEE Profile</a></p>
                            <?php
                            foreach ($RequestCandidateFields as $RequestCandidateField) {
                                if ($RequestCandidateField['RequestCandidateField_RequestCandidate'] == $RequestCandidate['RequestCandidate_ID']) {
                                    ?>
                                    <p><b><?= $RequestCandidateField['RequestCandidateField_Field'] ?></b></p>
                                    <p><?= Str_replace(['\r\n', "\'", '\"'], ['<br>', "'", '"'], $RequestCandidateField['RequestCandidateField_Value']) ?></p>
                                    <?php
                                }
                            }
                            ?>
                            <hr>
                            <?php if ($CheckAccessVote) { ?>
                                <?php if ($status == 0) { ?>
                                    <p><b>Voting of Senior delegates</b> <?= Short_Name($Delegate['Delegate_Name']) ?></p>
                                    <form method="POST" action="<?= PageAction('Delegate.Candidate.Vote') ?>">    
                                        <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID'] ?>'>    
                                        <?php
                                        $vote = -2;
                                        $reason = '';
                                        if (isset($RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$Delegate['Delegate_ID']])) {
                                            $vote = $RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$Delegate['Delegate_ID']];
                                            $reason = $RequestCandidateVoteReason[$RequestCandidate['RequestCandidate_Competitor']][$Delegate['Delegate_ID']];
                                        }
                                        ?>
                                        <p>I'm thinking <input type="radio" <?= $vote == -2 ? 'checked' : '' ?> name="Status" value="-2"></p>
                                        <p>I accept <input type="radio" <?= $vote == 1 ? 'checked' : '' ?> name="Status" value="1"> <i class="fas fa-thumbs-up"></i></p>
                                        <p>I abstained <input type="radio" <?= $vote == 0 ? 'checked' : '' ?> name="Status" value="0"> <i class="fas fa-balance-scale"></i></p>
                                        <p>I decline <input type="radio" <?= $vote == -1 ? 'checked' : '' ?> name="Status" value="-1"> <i class="fas fa-ban"></i></p>
                                        <p>Reason <input type="input" value="<?= $reason ?>" name="Reason"> </p>
                                        <p><button><i class="fas fa-vote-yea"></i> Vote</button></p>
                                    </form> 
                                    <hr>
                                <?php } ?>
                                <?php
                                $accept = 0;
                                $decline = 0;
                                $think = 0;
                                $abstain = 0;
                                if (isset($RequestCandidateVoteReasons[$RequestCandidate['RequestCandidate_Competitor']])) {
                                    foreach ($RequestCandidateVoteReasons[$RequestCandidate['RequestCandidate_Competitor']] as $row) {
                                        ?>
                                        <p><b><?= $row['Name'] ?></b>
                                            <?php if ($row['Status'] == -1) { ?>
                                                <?php $decline++; ?>
                                                <i class="fas fa-ban"></i> Declined
                                            <?php } ?> 
                                            <?php if ($row['Status'] == -0) { ?>
                                                <?php $abstain++; ?>
                                                <i class="fas fa-balance-scale"></i> Abstained
                                            <?php } ?>
                                            <?php if ($row['Status'] == 1) { ?>
                                                <?php $accept++; ?>
                                                <i class="fas fa-thumbs-up"></i> Accepted
                                            <?php } ?>
                                            <?php if ($row['Status'] == -2) { ?>
                                                <?php $think++; ?>
                                                Thinking
                                            <?php } ?> 
                                            <?php if ($row['Reason']) { ?>
                                                -   <?= $row['Reason']; ?>
                                            <?php } ?>
                                        </p>    
                                        <?php
                                    }
                                }
                            }
                            ?>                    
                            <?php if ($status == 0 and $CheckAccessDecline and $decline > 0 and $accept == 0 and $think == 0 and ( $decline + $accept + $abstain) == sizeof($Seniors)) { ?>
                                <form method="POST" action="<?= PageAction('Delegate.Candidate.Decline') ?>" onsubmit="return confirm('Confirm reject')">
                                    <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID'] ?>'>    
                                    <p> Reject application <button class="delete"><i class="fas fa-user-alt-slash"></i> Reject</button></p>
                                </form>
                            <?php } ?>        
                            <?php if ($status == 0 and $CheckAccessAccept and $decline == 0 and $accept > 0 and $think == 0 and ( $decline + $accept + $abstain) == sizeof($Seniors)) { ?>
                                <form method="POST" action="<?= PageAction('Delegate.Candidate.Accept') ?>" onsubmit="return confirm('Confirm accept')">
                                    <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID'] ?>'>    
                                    <p>Accept application <button><i class="fas fa-user-plus"></i> Accept</button></p>
                                </form>
                            <?php } ?>    
                            <?php if ($status == -1 and $CheckAccessDecline) { ?>
                                <form method="POST" action="<?= PageAction('Delegate.Candidate.Return') ?>" onsubmit="return confirm('Confirm return')">
                                    <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID'] ?>'>    
                                    <p>Return application <button><i class="fas fa-undo-alt"></i> Return</button></p>
                                </form>
                            <?php } ?>  
                        </span>    
                    <?php } ?>
            </td></tr></table>    

<?php } ?>